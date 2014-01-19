<?php

namespace Gateway\Handler\Erp\Etron\XML\Reader;

use Gateway\Handler\Erp\Etron\XML\Reader,
    Gateway\IHandler,
    Gateway\Utils;

/**
 * Products XML to DataSource handler.
 *
 * @author Lukas Bruha
 */
class Products extends Reader {

    protected $type = IHandler::TYPE_PRODUCTS;

    /**
     * Debug helper.
     * 
     * @var array
     */
    protected $debug = array(
                        'limit' => false,
                        'oldCategory' => false,
    );

    
    /**
     * Valid attributes for connection.
     * 
     * @var array
     * 
     */
    protected $validAttributes = array();
    
    
    /**
     * If true, null values of attribute does not allow 
     * associated products to be visible.
     * 
     * @var bool
     */
    protected $allowAttributeEmptyValues = false;
    
    /**
     * Parse given XML file and creates product structure.
     * 
     * @param \SimpleXml_Element $xml
     */
    protected function processXml($xml) {
        Utils::log("Processing products XML input...");
 
        $products = new \Gateway\DataSource\Products();

        $step = 0;

        // parent products helper
        $parentProducts = array();

        // if attributes mapping (valid attributes specification) is set,
        // we load valid attributes that will be passed and/or remapped together
        // with product
        if (isset($this->connection->mapping['attribute'])) {
            $this->validAttributes = array_keys($this->connection->mapping['attribute']);
        }
        
        $logAttrList = count($this->validAttributes) ? implode(", ", $this->validAttributes) : 'none'; 
        Utils::log(sprintf("Allowed products attribute(s): %s", $logAttrList));
        
        foreach ($xml->products as $xmlProduct) {
            // product description
            $xmlProductDescriptionPattern = '//products_description[@products_id=' . $xmlProduct['products_id'] . ']';
            $xmlProductDescription = $xml->xpath($xmlProductDescriptionPattern);
            $xmlProductDescription = current($xmlProductDescription);

            // 0 = simple, 1 = configurable
            $typeKey = (int) $xmlProduct['products_master_flag'];
            $isBundle = (isset($xmlProduct['products_bundle_flag']) && ((int) $xmlProduct['products_bundle_flag'])) ? true : false;
            
            //$isMaster = isset($xmlProduct['products_master_model']) && (int) $xmlProduct['products_master_model'] ? true : false;
            $attributeSet = "default"; // FIXME put this information into XML as eg. <attributes products_id="13352" set="tshirt">

            $product = $this->createProduct($typeKey, $isBundle);
            $product->id = (int) $xmlProduct['products_id'];
            $product->sku = (string) $xmlProduct['products_model'];
            $product->price = (string) $xmlProduct['products_regularprice'];
            $product->quantity = (string) $xmlProduct['products_quantity'];
            $product->isVisible = (string) $xmlProduct['products_visibility']; 
            $product->weight = (string) $xmlProduct['products_weight'];
            $product->isEnabled = ((int) $xmlProduct['products_status']) === 1 ? true : false; // 1 = Enabled, 2 = Disabled
			$product->taxClassId = (int) $xmlProduct['products_tax_class_id']; 
            
            // Magento dependent properties so we create dynamic ones
            $product->addSpecialProperty('attributeSet', $attributeSet);
            //$product->addSpecialProperty('taxClassId', (int) $xmlProduct['products_tax_class_id']);
            //$product->addSpecialProperty('taxClassId', 2);
            $product->addSpecialProperty('specialPrice', (string) $xmlProduct['products_price']);
            $product->addSpecialProperty('shippingTime', (string) $xmlProduct['products_shippingtime']);
            $product->addSpecialProperty('ean', (string) $xmlProduct['products_ean']);
            
            // localized description 
            $xmlProductDescriptionPattern = '//products_description[@products_id=' . $product->id . ']';
            $xmlProductDescription = $xml->xpath($xmlProductDescriptionPattern);

            foreach ($xmlProductDescription as $xmlDesc) {
                $lang = \Gateway\DataSource\Entity\Product\Attribute::NOT_LOCALIZED;
                
                // if lang isset, we pass it, else default one is left
                if (isset($xmlDesc['language_id'])) {
                    $lang = (string) $xmlDesc['language_id'];
                }

                $description = new \Gateway\DataSource\Entity\Product\Description($lang);
                $description->name = (string) $xmlDesc['products_name'];
                $description->description = trim((string) $xmlDesc['products_description']);
                $description->shortDescription = trim((string) $xmlDesc['products_short_description']);
                $description->metaTitle = trim((string) $xmlDesc['products_name']); // metaTitle not set in XML
                $description->metaDescription = trim(strip_tags((string) $xmlDesc['products_short_description'])); // metaDescription not set in XML
                $description->metaKeywords = trim((string) $xmlDesc['products_keywords']);

                $product->addDescription($description);
            }

            $this->loadCategories($xml, $xmlProduct, $product);
            // searches all current product categories IDs
            /*$xmlProductCategoriesPattern = '//products_to_categories[@products_id=' . $xmlProduct['products_id'] . ']/@categories_id';
            $productCategoriesIds = $xml->xpath($xmlProductCategoriesPattern);

            // FIXME what if no categories were found? Skip this product?
            if ($productCategoriesIds) {
                foreach ($productCategoriesIds as $productCategoryId) {
                    
                    if ($this->debug['oldCategory']) {
                        // old not localized
                        $category = $this->findCategory($productCategoryId, $xml);
                        $product->addCategory($category);
                    } else {
                        $childNode = current($xml->xpath('//categories[@categories_id=' . $productCategoryId . ']'));
                        $childNodeDescriptions = $xml->xpath('//categories_description[@categories_id=' . $productCategoryId . ']');

                        // common attributes for all localized
                        $category = new \Gateway\DataSource\Entity\Product\Category();
                        $category->id = (int) $productCategoryId;
                        $category->isActive = ((int) $childNode['categories_status']) > 0 ? true : false;
                        $category->isVisible = ((int) $childNode['categories_visibility']) > 0 ? true : false; 
                        $category->addSpecialProperty('isAnchor', false);

                        // localized values
                        foreach ($childNodeDescriptions as $childNodeDescription) {
                            $category->name = (string) $childNodeDescription['categories_name'];
                            $category->lang = (string) $childNodeDescription['language_id'];

                            // build localized parent path
                            $parentId = (int) $childNode['parent_id'];

                            if ($parentId > 0) {
                                $category->parent = $this->buildPath($parentId, $xml, (string )$childNodeDescription['language_id']);
                            }

                            $product->addCategory(clone $category);
                        }
                    }
                }
                    
            }*/
            
            /*
            dump($product->getCategories());
            exit;*/

            if ($product->type == \Gateway\DataSource\Entity\IProduct::TYPE_BUNDLE) {
                // FIXME currently bundle products are skipped
                Utils::log(sprintf("Skipping bundle product '%s'.", $product->sku));
                continue;
                
                Utils::log(sprintf("Creating bundle product '%s'.", $product->sku));
                
                $xmlProductsSetsPattern = '//products_sets[@products_model_bundle= ' . $product->sku . ']/@products_model';
                $xmlProductsSets = $xml->xpath($xmlProductsSetsPattern);
                
                foreach ($xmlProductsSets as $productSku) {
                    $productSku = (string) $productSku;
                    
                    // configurable product
                    $associated = $products->get($productSku);
                    
                    if ($associated) {
                        $product->addAssociated($associated);
                    }
                    
                    $parentProducts[$product->sku] = array();
                    $parentProducts[$product->sku]['product'] = $product;
                    $parentProducts[$product->sku]['skus'] = $product->getAssociated(true);                    
                }
                
                Utils::log(sprintf("Bundle subproducts skus: '%s'.", implode(",", $product->getAssociated(true))));
            }
            
            // this belongs only to Configurable product 
            if ($product->type == \Gateway\DataSource\Entity\IProduct::TYPE_CONFIGURABLE) {
                $xmlSimpleProductsPattern = '//products[@products_master_model= ' . $product->sku . ']/@products_model';
                $xmlSimpleProductsSkus = $xml->xpath($xmlSimpleProductsPattern);

                // we store products skus to later construct children and parents
                $parentProducts[$product->sku] = array();
                $parentProducts[$product->sku]['product'] = $product;
                $parentProducts[$product->sku]['skus'] = (array) $xmlSimpleProductsSkus;
            } //else {
                // this is for Simple ones
                $xmlProductAttributesPattern = '//product_keys/attributes[@products_id=' . $xmlProduct['products_id'] . ']';
                $productAttributes = current($xml->xpath($xmlProductAttributesPattern));
  
                // creating product attributes objects and adding them to product
                if ($productAttributes !== false) {
                    
                    $logValid = array();
                    $logInvalid = array();
                    
                    foreach ($productAttributes as $productAttribute) {
                        $lang = \Gateway\DataSource\Entity\Product\Attribute::NOT_LOCALIZED;
                        $label = isset($productAttribute['label']) ? (string) $productAttribute['label'] : false;
                        $name = (string) $productAttribute['name'];
                        $value = (string) $productAttribute['value'];

                        // if empty value and empty values are not allowed, skip it
                        // FIX in XML
                        if (!$value && !$this->allowAttributeEmptyValues) {
                            continue;
                        }
                        
                        // if lang isset, we pass it, else default one is left
                        if (isset($productAttribute['language_id'])) {
                            $lang = (string) $productAttribute['language_id'];
                        }

                        // pass only valid attributes defined in mapping attribute structure
                        if (in_array($name, $this->validAttributes)) { 
                            $attr = new \Gateway\DataSource\Entity\Product\Attribute($lang, $name, $value, $label);                    
                            $product->addAttribute($attr);
                            
                            $logValid[$name] = $name;
                        } else {
                            // log skipped invalid attributes in XML
                            $logInvalid[$name] = $name;
                        }
                        
                    }
                    
                    $msg = sprintf("%s attributes: valid='%s', invalid='%s'", $product->sku, implode(", ", $logValid), implode(", ", $logInvalid));
                    Utils::log(\Logger\ILogger::DEBUG, $msg);
                //}
            }

            // MANUFACTURER
            if (isset($xmlProduct['manufacturers_id']) && $xmlProduct['manufacturers_id']) {
                $xmlManufacturerPattern = '//manufacturers[@manufacturers_id=' . $xmlProduct['manufacturers_id'] . ']';
                $xmlManufacturer = current($xml->xpath($xmlManufacturerPattern));

                if ($xmlManufacturer) {
                    $product->manufacturer = (string) $xmlManufacturer['manufacturers_name'];
                }
            }

            // IMAGES
            // images names must be converted from IMAGE_01.jpg[;IMAGE_0n.jpg] to array
            $xmlImagesNames = (string) $xmlProduct['products_image'];
            
            // if images found, we pass them
            if ($xmlImagesNames) {
                $xmlImagesNames = explode(";", $xmlImagesNames);

                foreach ($xmlImagesNames as $index => $image) {
                    // always the first image is thumbnail, small etc. in XML
                    if ($index == 0) {
                        $product->addImage($image, \Gateway\DataSource\Entity\Product\IImage::TYPE_PREVIEW);
                        $product->addImage($image, \Gateway\DataSource\Entity\Product\IImage::TYPE_PREVIEW_MEDIUM);
                        $product->addImage($image, \Gateway\DataSource\Entity\Product\IImage::TYPE_PREVIEW_SMALL);
                    } else {
                        // the rest is gallery type
                        $product->addImage($image);
                    }
                }
            }

            // RELATED, UPSELL AND CROSS-SELL PRODUCT SKUS
            $product->addSpecialProperty('reSkus', (string) $xmlProduct['products_relations_related']);
            $product->addSpecialProperty('usSkus', (string) $xmlProduct['products_relations_up']);
            $product->addSpecialProperty('csSkus', (string) $xmlProduct['products_relations_cross']);
            
            // and adding to collection
            $products->add($product);
            
            /*dump($product);
            exit;*/
            
            // debug limit
            if ($this->debug['limit'] && ($step++ == $this->debug['limit'])) {
                break;
            }
        }
        
        Utils::log("%s products has been parsed.", $products->count());

        // FIXME when subproducts does not exists, skip? 
        // now it is time to put parents and children into this structure        
        foreach ($parentProducts as $parent) {
            // if parent has children, we go through
            if (isset($parent['skus']) && count($parent['skus'])) {
                Utils::log("Updating '%s': adding subproducts of '%s'.", $parent['product']->sku, implode(", ", $parent['skus']));

                foreach ($parent['skus'] as $childSku) {
                    // if child found in products...
                    $child = $products->get($childSku);

                    // ...lets add child its parent and parent its child
                    if ($child) {
                        $parent['product']->addAssociated($child);
                        $child->setParent($parent['product']);

                        Utils::log("'%s': subproduct of '%s' added.", $parent['product']->sku, $child->sku);
                    }
                }
            }
        }

        Utils::log("Products DataSource is prepared.");

        return $products;
    }

    protected function loadCategories($xml, $xmlProduct, $product) {
        // empty
    }
    
    /**
     * Product factory.
     * 
     * @param int $typeKey
     * @return \Gateway\DataSource\Entity\Product\Simple
     */
    protected function createProduct($typeKey = 0, $isBundle = false) {
        // FIXME use mapping for this
        if ($isBundle) {
            $product = new \Gateway\DataSource\Entity\Product\Bundle();
        } elseif ($typeKey == 1) {
            $product = new \Gateway\DataSource\Entity\Product\Configurable();
        } else {
            $product = new \Gateway\DataSource\Entity\Product\Simple();
        }

        return $product;
    }
    
    /**
     * Builds tree path of category.
     * 
     * @param int $productCategoryId
     * $param \SimpleXmlElement $xml
     * $param int $langId
     * @return \Gateway\DataSource\Entity\Product\Category
     */
    /*protected function buildPath($productCategoryId, $xml, $langId = null) {
        $childNode = current($xml->xpath('//categories[@categories_id=' . $productCategoryId . ']'));

        if ($langId !== null || $langId !== false) {
            $childNodeDescriptions = $xml->xpath('//categories_description[@categories_id=' . $productCategoryId . ' and @language_id=' . $langId .']');
        } else {
            $childNodeDescriptions = $xml->xpath('//categories_description[@categories_id=' . $productCategoryId . ']');
        }
        
        foreach ($childNodeDescriptions as $childNodeDescription) {
            $category = new \Gateway\DataSource\Entity\Product\Category();
            $category->id = (int) $productCategoryId;
            $category->name = (string) $childNodeDescription['categories_name'];
            $category->isActive = ((int) $childNode['categories_status']) > 0 ? true : false;
            $category->isVisible = ((int) $childNode['categories_visibility']) > 0 ? true : false; 
            $category->addSpecialProperty('isAnchor', false);
            
            if ($langId && isset($childNodeDescription['language_id'])) {
                $category->lang = (string) $childNodeDescription['language_id'];
            }
            
            // we set parent only if not root node
            $parentId = (int) $childNode['parent_id'];
            if ($parentId > 0) {
                $category->parent = $this->buildPath($parentId, $xml, $langId);
            }
        }
        
        return $category;
    }*/
    
    /**
     * Searches for product's category and creates the whole structure on the fly.
     * 
     * @param int $categoryId
     * @param \SimpleXml_Element $xml
     * @return \Gateway\DataSource\Entity\Product\Category
     */
    /*protected function findCategory($categoryId, $xml) {
        $childNode = current($xml->xpath('//categories[@categories_id=' . $categoryId . ']'));
        $childNodeDescriptions = $xml->xpath('//categories_description[@categories_id=' . $categoryId . ']');
        
        foreach ($childNodeDescriptions as $childNodeDescription) {
            $category = new \Gateway\DataSource\Entity\Product\Category();
            $category->id = (int) $categoryId;
            $category->name = (string) $childNodeDescription['categories_name'];
            $category->isActive = ((int) $childNode['categories_status']) > 0 ? true : false;
            $category->isVisible = ((int) $childNode['categories_visibility']) > 0 ? true : false; 
            $category->addSpecialProperty('isAnchor', false);
            //$category->lang = (string) $childNodeDescription['language_id'];

            // we set parent only if not root node
            if ((int) $childNode['parent_id'] > 0) {
                $category->parent = $this->findCategory($childNode['parent_id'], $xml);
            }
        }

        return $category;
    }*/

    /**
     * Validates input and tries to load XML from it.
     * 
     * @param mixed $input
     * @return boolean
     * @throws \Nette\IOException
     */
    public function validate($input) {
        $xml = parent::validate($input);
        
        if ($xml) {

            // very simple validation - just search for products inside
            if (!count($xml->products)) {
                Utils::log("Invalid input format. XML does not contain required 'products' element.");
                throw new \Nette\IOException("Invalid input format. XML does not contain required 'products' element.");
            }
              
        } 
        
        return $xml;  
    }
}