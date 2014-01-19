<?php

namespace Gateway\Handler\Shop\Magento\Magmi\Writer;

use Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Product;

/**
 * Products DataSource to Magmi interface handler.
 *
 * @author Lukas Bruha
 */
class Products extends \Gateway\Handler\Shop\Magento\Magmi\Writer {

    protected $type = IHandler::TYPE_PRODUCTS;
    
    /**
     * Helper for debugging.
     * 
     * @var bool
     */        
    protected $isTest = false;
    
    /**
     * Converts XML to DataSource.
     * 
     * @return int Number of processed items.
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");
        Utils::log("Processing DataSource to Magmi format...");
        
        // no datasource given
        if (!$this->dataSource) {
            Utils::log(\Logger\ILogger::WARNING, "No datasource given. Skipping...");
            return;
        }
        
        // invalid datasource format
        if (!($this->dataSource instanceof \Gateway\DataSource\Products)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Products", get_class($this->dataSource)));
        }

        // a helper to avoid processing the same product if already processed (eg. children of Configurable)
        $productsLog = array();
        $magmiItems = array();
        
        // items to be run via Magmi import again when relations found due tu problems
        // with relating product that is not in database yet
        $magmiRelatedItems = array(); 

        Utils::log("Processing DataSource to Magmi format...");

        // we go through all products in datasource and re-map each to Magni item
        foreach ($this->dataSource as $product) {
            //Debugger::dump($product);
            // special products log
            if (!isset($productsLog[$product->type])) {
                $productsLog[$product->type] = 0;
            }

            // to make it store view based, iterater over some localized description 
            // that is required for all products
            if (!$product->hasDescription()) {
                Utils::log(\Logger\ILogger::WARNING, "Data inconsistency: missing description for product ID '%s', SKU '%s'.", $product->id, $product->sku);

                // missing description means to skip Simple one as invalid,
                // but for the rest, eg. Configurable, it means to abort the whole import
                if ($product instanceof \Gateway\DataSource\Entity\Product\Simple) {
                    Utils::log(\Logger\ILogger::WARNING, "Product ID '%s' having SKU '%s' has been skipped.", $product->id, $product->sku);
                    continue;
                } else {
                    Utils::log(\Logger\ILogger::ERROR, "Import aborted due to not complete data.");
                    break;
                }
            }
            
            // used only for logging
            $productsLog[$product->type]++;

			// Tax classes will be mapped at the GW Connection - Property mapping  NB 12.07.2013
			$this->connection->applyPropertyMapping($product->taxClassId, 'product.tax_class_id');
			
            // GLOBAL AND WEBSITE SCOPE
            $item = array(
                // global scope
                "sku" => $product->sku,
                "type" => $product->type,
                "price" => $product->price ? $product->price : NULL,
                "qty" => $product->quantity,
                
                // special hard-coded attributes
                'shipping_time' => $product->getSpecialProperty('shippingTime'),
                'ean' => $product->getSpecialProperty('ean'),
                
                // website scope
                "status" => $product->isEnabled ? 1 : 2, // 1 = Enabled, 2 = Disabled
                "tax_class_id" => (int) $product->taxClassId->value, // 0 = None, 2 = Taxable, 4 = Shipping - NOT VALID, see comment above NB 12.07.2013
                "weight" => $product->weight,
                "manufacturer" => $product->manufacturer,
                // "group_price"
            );

            $item["special_price"] = $product->getSpecialProperty('specialPrice') ? $product->getSpecialProperty('specialPrice') : NULL;
            
            // ATTRIBUTE SET
            if ($product->getSpecialProperty('attributeSet')) {
                $item["attribute_set"] = $product->getSpecialProperty('attributeSet');
            }

            // RELATED, UPSELL AND CROSS-SELL
            $reSkus = $product->getSpecialProperty('reSkus');
            $csSkus = $product->getSpecialProperty('csSkus');
            $usSkus = $product->getSpecialProperty('usSkus');

            // IMAGES
            // FIXME currently handled via products_images handler only
            /*if ($product->hasImages()) {
                $gallery = array();

                foreach ($product->getImages() as $type => $image) {
                    switch ($type) {
                        case IImage::TYPE_GALLERY:
                            $array = $image;

                            // not array given, we convert it
                            if (!is_array($image)) {
                                $array = array($image);
                            }

                            // let's create images gallery
                            foreach ($image as $gImage) {
                                $gallery[] = (string) $image;
                            }

                            break;
                        case IImage::TYPE_PREVIEW_MEDIUM:
                            $item['small_image'] = (string) $image;
                            break;                                
                        case IImage::TYPE_PREVIEW;
                            $item['image'] = (string) $image;
                            break;                                
                        case IImage::TYPE_PREVIEW_SMALL;
                            $item['thumbnail'] = (string) $image;
                            break;
                        default:
                            Utils::log(sprintf("No image type support for '%s'.", $image));
                            break;
                    }

                    if (count($gallery)) {
                        $image['media_gallery'] = implode(";", $gallery);
                    }
                }

                //dump($item); exit;
            }*/
            
            // STORE VIEW SCOPE
            // for each localized description we create new item
            // to be imported
            foreach ($product->getDescriptions() as $lang => $desc) {
                // applies localization/translation to languages
                $this->connection->applyLocalizationMapping($desc);
                
                // not localized means default description for all stores,
                // any other value means to pass it to the specific store (= $lang)
                if ($lang !== Product\Description::NOT_LOCALIZED) {
                    $item['store'] = $desc->lang;
                }

                $item["name"] = $desc->name;
                $item["description"] = $desc->description;
                $item["short_description"] = $desc->shortDescription;
                
                // FIXME visibility is now used for global scope, but it should be set for store view
                // Configurable ones is always set to 1 as Not Individually Visible
                //if ($product instanceof Product\Configurable) {
                    //$product->isVisible->value = 4;
                //} else { // else use what comes or remap
                    $this->connection->applyPropertyMapping($product->isVisible, 'product.visibility');
                //}
                    
                $item["visibility"] = (int) $product->isVisible->value;
                $item["meta_keyword"] = $desc->metaKeywords;
                $item["meta_title"] = strip_tags($desc->metaTitle ? $desc->metaTitle : $desc->name);
                $item["meta_description"] = strip_tags($desc->metaDescription ? $desc->metaDescription : $desc->shortDescription);
                
                // "image"
                // a chain of localized categories passed as string
                // NOTICE: if localized does not exists, default is used
                //$item["categories"] = $this->convertCategories($product->getCategories($lang));
                
                /*if (isset($item['store'])) {
                    $item["categories"] = $item['store'] . "/" . $item["categories"];
                }*/
                
                // localized attributes but attribute set depended
                // if not setup in attribute set, it will not be passed
                $attrs = $product->getAttributes($lang);
 
                if (count($attrs)) {
                    foreach ($attrs as $key => $attr) {  
                        // applies localization/translation to languages - NOT USED
                        $this->connection->applyLocalizationMapping($attr);

                        // apply mapping tables
                        $this->connection->applyAttributeMapping($attr, 'product.attributes');

                        // we pass mapped name and value
                        $item[$attr->code] = $attr->value;
                    }
                }
                
                // non localized attributes
                if ($lang !== Product\Description::NOT_LOCALIZED) {
                    $attrs = $product->getAttributes(Product\Description::NOT_LOCALIZED);

                    if (count($attrs)) {
                        foreach ($attrs as $key => $attr) {  
                            // apply mapping tables
                            $this->connection->applyAttributeMapping($attr, 'product.attributes');

                            // we pass mapped name and value
                            $item[$attr->code] = $attr->value;
                        }
                    }
                }
                
                // for Configurable we have to extract used attributes in all Simple products
                // to be passed correctly
                if ($product instanceof Product\Configurable) {
                    $usedAttributes = $this->extractAttributesOfConfigurable($product, $lang);

                    // TODO add manufacturer
                    if (count($usedAttributes)) {
                        $usedAttributesCodes = array_keys($usedAttributes);

                        $item["configurable_attributes"] = implode(",", $usedAttributesCodes);

                        // FIXME extract also SKUs of used subproducts
                        $subproductsSKUs = array_keys(current($usedAttributes));
                        $item["simples_skus"] = implode(",", $subproductsSKUs);
                    }
                } 

                // FIRST LOOP
                // for each localized description we create new item
                // to be imported      
                $magmiItems[] = $item; 
                
                // SECOND LOOP
                $updateItem = array();                    
                $updateItem['sku'] = $item['sku'];        
                $updateItem['type'] = $item['type'];     
                $updateItem['price'] = $item['price'];
                $updateItem['qty'] = $item['qty'];
                $updateItem["special_price"] = $item["special_price"];
                $updateItem['store'] = null;

                // by default remove all previous
                $previousRemoveRegexp =  "-re::.*";                    
                $updateItem["re_skus"] = $previousRemoveRegexp;
                $updateItem["cs_skus"] = $previousRemoveRegexp;
                $updateItem["us_skus"] = $previousRemoveRegexp;
                    
                if ($reSkus || $csSkus || $usSkus) {
                    if ($reSkus) {
                        $updateItem["re_skus"] .=  "," . str_replace(";", ",", $reSkus);
                    }

                    if ($csSkus) {
                        $updateItem["cs_skus"] .= "," . str_replace(";", ",", $csSkus);
                    }

                    if ($usSkus) {
                        $updateItem["us_skus"] .= "," . str_replace(";", ",", $usSkus);
                    }
                }
                    
                $magmiRelatedItems[] = $updateItem;
                
                Utils::log(\Logger\ILogger::DEBUG, "Product '%s' for StoreView of '%s'.", $item['sku'], $desc->lang);
            }
        }

        // executes Magmi import
        if (!$this->isTest) {
            Utils::log(">>> BASIC IMPORT <<<"); 
            $this->import($magmiItems);
            Utils::log("Import done."); 
            
            // run import again but with items having relations only
            if (count($magmiRelatedItems)) {
                Utils::log(">>> RELATIONS UPDATE <<<"); 
                Utils::log("Running import again for %s related items only...");
                
                // set update mode
                $this->import($magmiRelatedItems, true);
                
                Utils::log("Relations updated."); 
            }
        }
        
        // statistics report
        $count = $this->processProductsLog($productsLog);
        
        return $count;
    }

    // FIXME currently used separately
    /*protected function convertImages($images) {
        foreach ($images as $type => $image) {
            if ($type == IImage::TYPE_GALLERY) {
                // gallery does not have one image but more
                $this->convertImages($image);
            } else {

            }
            dump($product->getImages());
        }
    }*/
    
    /**
     * Converts categories collection to expected Magmi string format.
     * 
     * @param array $categories
     * @return string
     */
    protected function convertCategories($categories) {
        $res = array();

        // expected <CATEGORY_NAME>::[is_active]::[is_anchor]::[include_in_menu]
        foreach ($categories as $category) {
            $path = array();
            $current = $category;
            
            while ($current) {
                // we replace by is_active::is_anchor::include_in_menu
                $options = sprintf('::%s::%s::%s', (int) $current->isActive, (int) $current->getSpecialProperty('isAnchor'), (int) $current->isVisible);
                $name = $current->name;

                $path[] = $name . $options;
                
                $current = $current->getParent();
            }

            // add category to array
            $res[] = implode("/", array_reverse($path));
            
            // FIXME currently not used
            /*$catOptions = array(
                'is_active' => (int) $category->isActive,
                'is_anchor' => (int) $category->getSpecialProperty('isAnchor'),
                'include_in_menu' => (int) $category->isVisible,
            );

            $res[] = 'test/' . $category->name . '::' . implode("::", $catOptions);
             */
        }
        

        $cats = implode(";;", $res);
        
        return $cats;
    }

    /**
     * Extracts used attributes of configurable product and creates
     * a structure of [ATTRIBUTE_CODE[SKU: ATTRIBUTE_VALUE]*]* 
     * 
     * @param \Gateway\DataSource\Entity\Product\Configurable $product
     * @param string $lang
     * @return array
     */
    protected function extractAttributesOfConfigurable(Product\Configurable $product, $lang) {
        $res = array();

        foreach ($product->associated as $subproduct) {
            $attrs = $subproduct->getAttributes($lang);

            foreach ($attrs as $attr) {
                // apply mapping tables
                $this->connection->applyAttributeMapping($attr, 'product.attributes');
                
                if (!isset($res[$attr->code])) {
                    $res[$attr->code] = array();
                }

                $res[$attr->code][$subproduct->sku] = $attr->value;
            }
        }

        return $res;
    }

    /**
     * Just processes products log.
     * 
     * @param array $productsLog
     */
    private function processProductsLog($productsLog) {
        // reports summary     
        $statsProducts = call_user_func(function($productsLog, $glue = ": ") {
                    $res = array();

                    foreach ($productsLog as $key => $value) {
                        $res[] = $key . $glue . $value;
                    }

                    return $res;
                }, $productsLog);

        $statsSummary = array_sum($productsLog);

        Utils::log("Finished with %s, total %s.", implode(",", $statsProducts), $statsSummary);
        
        return $statsSummary;
    }

}

