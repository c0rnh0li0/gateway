<?php

namespace Gateway\Handler\Erp\Rakuten\XML\Reader;

use Gateway\Handler\Erp\Rakuten\XML\Reader,
    Gateway\IHandler,
    Gateway\Utils, 
	Rakuten\Handlers;

/*
 * 
 * 
 * manufacutrer_name instead of manufacturer id 
 * 
 * 'producer' => 'manufacturers_id'
 * 
 */


/**
 * Products XML to DataSource handler.
 *
 * @author Darko Krstev
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
		
		$all_products = array();
		
        foreach ($xml->products as $xmlProduct) {
        	// skip it if it is a child product        	
			if (strlen((string) $xmlProduct['products_master_model']) > 0)
				continue;
						
        	// 0 = simple, 1 = configurable
            $typeKey = (int) $xmlProduct['products_master_flag'];
            $isBundle = (isset($xmlProduct['products_bundle_flag']) && ((int) $xmlProduct['products_bundle_flag'])) ? true : false;
			
        	$product = $this->createProduct($typeKey, $isBundle);
			
			$product_data_array = $this->buildProduct($xml, $xmlProduct);
			
			/* Deal with variants */
			$product_variants = array();
			
			if ((int) $xmlProduct['products_master_flag'] == 1){
				$xmlProductMasterPattern = '//products[@products_master_model=' . $xmlProduct['products_model'] . ']';
		        $productVariants = $xml->xpath($xmlProductMasterPattern);
				
		        // creating product attributes arrays and adding them to product
		        if ($productVariants !== false) {
		            foreach ($productVariants as $productVariant) {
		            	$variant_data_array = $this->buildProduct($xml, $productVariant);
		                $temp_variant = $product->buildProduct($productVariant, $variant_data_array, true);
						$temp_variant['attributes'] = $variant_data_array['attributes'];
						$temp_variant['categories'] = $variant_data_array['categories'];
						$temp_variant['images'] = $variant_data_array['images'];
						
						$product_variants[] = $temp_variant;
						unset($temp_variant);
		            }
		        }
			}
			
			$product_data = $product->buildProduct($xmlProduct, $product_data_array);
			//$product_data = array_merge($product_data, $product_data_array);
			 
			//$product_data['products_description'] = $product_data_array['descriptions'];
			$product_data['attributes'] = $product_data_array['attributes'];
			$product_data['categories'] = $product_data_array['categories'];
			$product_data['images'] = $product_data_array['images'];
			
			/*
			for ($i = 0; $i < sizeof($product_data['attributes']); $i++){
				if ($product_data['attributes'][$i]['value'] == '')
					unset($product_data['attributes'][$i]);
			}
			*/
			
			if (sizeof($product_variants))
				$product_data['variants'] = $product_variants;
			

			// $product->sku = (string) $xmlProduct['products_model'];
			
			$all_products[] = $product_data;

            $this->loadCategories($xml, $xmlProduct, $product);
          
			  // debug limit
            if ($this->debug['limit'] && ($step++ == $this->debug['limit'])) {
                break;
            }
        }

		/*
        // MANUFACTURER
        if (isset($xmlProduct['manufacturers_id']) && $xmlProduct['manufacturers_id']) {
            $xmlManufacturerPattern = '//manufacturers[@manufacturers_id=' . $xmlProduct['manufacturers_id'] . ']';
            $xmlManufacturer = current($xml->xpath($xmlManufacturerPattern));

            if ($xmlManufacturer) {
                $product->manufacturer = (string) $xmlManufacturer['manufacturers_name'];
            }
        }
		*/

		Utils::log("%s products has been parsed.", count($all_products));

        // FIXME when subproducts does not exists, skip? 
        // now it is time to put parents and children into this structure        
        /*foreach ($parentProducts as $parent) {
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
        }*/

        Utils::log("Products DataSource is prepared.");

        //return $products;
        return $all_products;
    }

	protected function buildProduct($xml, $xmlProduct){
		/* Deal with descriptions */
        // product description
        $xmlProductDescriptionPattern = '//products_description[@products_id=' . $xmlProduct['products_id'] . ']';
        $xmlProductDescription = $xml->xpath($xmlProductDescriptionPattern);
        $xmlProductDescription = current($xmlProductDescription);
		
		$descriptions = array(
			'name' => (string) $xmlProductDescription['products_name'],
			'description' => trim((string) $xmlProductDescription['products_description']),
			'shortDescription' => trim((string) $xmlProductDescription['products_short_description']),
			'metaTitle' => trim((string) $xmlProductDescription['products_name']),
			'metaDescription' => trim(strip_tags((string) $xmlProductDescription['products_short_description'])),
			'metaKeywords' => trim((string) $xmlProductDescription['products_keywords'])
		);

		/* Deal with attributes */
		$xmlProductAttributesPattern = '//product_keys/attributes[@products_id=' . $xmlProduct['products_id'] . ']';
        $productAttributes = current($xml->xpath($xmlProductAttributesPattern));
  		$available_attributes = array();
                
        // creating product attributes arrays and adding them to product
        if ($productAttributes !== false) {
            //$logValid = array();
            //$logInvalid = array();
            
            foreach ($productAttributes as $productAttribute) {
                $lang = \Gateway\DataSource\Entity\Product\Attribute::NOT_LOCALIZED;
      				if (isset($productAttribute['language_id']))
                    $lang = (string) $productAttribute['language_id'];
				
				$available_attributes[] = array(
					'label' => isset($productAttribute['label']) ? (string) $productAttribute['label'] : false, 
					'name' => (string) $productAttribute['name'], 
					'value' => (string) $productAttribute['value'], 
					'lang' => $lang
				);
            }
        }

		/* Deal with categories */
		$xmlProductCategoriesPattern = '//products_to_categories[@products_id=' . $xmlProduct['products_id'] . ']';
        $productCategories = $xml->xpath($xmlProductCategoriesPattern);
  		$product_categories = array();
                
        // creating product attributes arrays and adding them to product
        if ($productCategories !== false) {
            foreach ($productCategories as $productCategory) {
                $product_categories[] = (string) $productCategory['categories_id'];
            }
        }
		
		
		// IMAGES
        // images names must be converted from IMAGE_01.jpg[;IMAGE_0n.jpg] to array
        $xmlImagesNames = (string) $xmlProduct['products_image'];
        $product_images = array();
        // if images found, we pass them
        if ($xmlImagesNames) {
            $xmlImagesNames = explode(";", $xmlImagesNames);

            foreach ($xmlImagesNames as $index => $image) {
            	$product_images[] = $image;
            }
        }
		
		return array(
				'products_description' => $descriptions, 
				'attributes' => $available_attributes, 
				'categories' => $product_categories, 
				'images' => $product_images
			);
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
    	require_once(WWW_DIR . "/../libs/Rakuten/Handlers/Products.php");
    	return new \Rakuten\Handlers\Products();
		
		/*
        // FIXME use mapping for this
        if ($isBundle) {
            $product = new \Gateway\DataSource\Entity\Product\Bundle();
        } elseif ($typeKey == 1) {
            $product = new \Gateway\DataSource\Entity\Product\Configurable();
        } else {
            $product = new \Gateway\DataSource\Entity\Product\Simple();
        }

        return $product;
		*/ 
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