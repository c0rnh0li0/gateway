<?php
namespace Gateway\Handler\Erp\Rakuten\XML\Reader;

use Gateway\Handler\Erp\Rakuten\XML\Reader,
    Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Localized,
    Gateway\DataSource\Entity\Category;

/**
 * Categories XML to DataSource handler.
 *
 * @author Darko Krstev
 */
class Categories extends Reader {
    
    protected $type = IHandler::TYPE_CATEGORIES;

    //protected $expectedOptions = array('loadProducts');
    protected $expectedOptions = array();
	
	protected $categories = array();
    
     /**
     * Converts XML to DataSource.
     * 
     */
    public function processXml($xml) {
        $ds = new \Gateway\DataSource\Categories();
        
        Utils::log("Processing categories XML input...");
 
        // 1) search for root
        // $rootCategoriesPattern = '//categories[@parent_id=0]';
        $rootCategoriesPattern = '//categories';
        $rootCategories = $xml->xpath($rootCategoriesPattern);
        
        // no root => inconsistency
        if (!count($rootCategories)) {
            $msg = "Categories in XML are inconsistent - missing root categories having parent_id=0.";
            
            Utils::log($msg);
            throw new \Nette\InvalidStateException($msg);
        }
        
        // process deep search for children to build up the structure
        foreach ($rootCategories as $node) {
        	$tmp_category = $this->processCategoryLocalization($ds, $xml, $node);
			//$tmp_category['products'] = $this->loadAssignedProducts($xml, $tmp_category);
			
            //$this->categories[] = $this->processCategoryLocalization($ds, $xml, $node);
			
			//$temp_category['children'] = $this->processChildren($ds, $xml, $temp_category);
            //$category = $this->createCategoryBase($node);
            //$this->categories[] = $this->processCategoryLocalization($ds, $xml, $node);
			
			
			$this->categories[] = $tmp_category;
        }
		
		$parents = array();
		foreach ($this->categories as $key => $row)
		{
		    $parents[$key] = $row['external_parent_shop_category_id'];
		}
		array_multisort($parents, SORT_ASC, $this->categories);
		
		Utils::log("%s categories has been parsed.", sizeof($this->categories));
        
        return $this->categories; //$ds;
    }
    
    /**
     * Creates base category information.
     * 
     * @param \SimpleXmlElement $node
     * @return \Gateway\DataSource\Entity\Category
     */
    protected function createCategoryBase($xml, $node) {
        //$category = new Category();
		
		$category = array(
			'shop_category_id' => '',						
			'external_shop_category_id' => (int) $node['categories_id'],
			'parent_shop_category_id' => '', 
			'external_parent_shop_category_id' => (int) $node['parent_id'], 
			'name' => '',
			'description' => '',
			
			'layout' => '',									
			'product_order' => '',							
															
			'visible' => ((int) $node['categories_visibility']) > 0 ? true : false,	
															
			'position' => (int) $node['sort_order'], 
			'url' => '', 
			'meta_title' => '', 
			'meta_description' => '', 
			'meta_keywords' => ''
		);
        
        //$category->id = (int) $node['categories_id'];
        //$category->isActive = ((int) $node['categories_status']) > 0 ? true : false;
        
        // ???????????/
        //$category->addSpecialProperty('isAnchor', false);

        // if loadProducts specified, we parse also products to categories assignments
        /*
        if ($this->options->getData('loadProducts')) {
            // load assigned products
            $products = $this->loadAssignedProducts($xml, $category);
            
            $category->addSpecialProperty('products', implode(',', $products));          
        }
        */
        return $category;
    }
    
    /**
     * Process category localized data.
     * 
     * @param \Gateway\DataSource\Categories $ds
     * @param \SimpleXmlElement $xml
     * @param \SimpleXmlElement $node
     * @param string $lang
     * @param \SimpleXmlElement $parent
     */
    protected function processCategoryLocalization($ds, $xml, $node, $parent = false) {
        $category = $this->createCategoryBase($xml, $node);
        
        //if ($parent) {
            //$category['external_parent_shop_category_id'] = $parent['external_shop_category_id'];
        //}
        
        $xmlCategoriesDescriptionPattern = '//categories_description[@categories_id=' . $category['external_shop_category_id'] . ']';       
        $xmlCategoriesDescription = current($xml->xpath($xmlCategoriesDescriptionPattern));
		
       	$category['name'] = (string) $xmlCategoriesDescription['categories_name'];
		$category['description'] = (string) $xmlCategoriesDescription['categories_description'];
		$category['meta_title'] = (string) $xmlCategoriesDescription['categories_name'];
		
        // localized values
        /*
        foreach ($xmlCategoriesDescription as $xmlCategoryDescription) {
            $localized = new \Gateway\DataSource\Entity\Category\LocalizedInfo();
            
            $localized->lang = isset($xmlCategoryDescription['language_id']) ? (string) $xmlCategoryDescription['language_id'] : Localized::NOT_LOCALIZED;
            $localized->name = (string) $xmlCategoryDescription['categories_name'];
            $localized->description = (string) $xmlCategoryDescription['categories_description'];
            
            $category->addLocalizedInfo($localized);
        }
        */
		
		return $category;
		
        // continue deeper
        //$this->processChildren($ds, $xml, $category);
    }
    
    /**
     * Processes localized children nodes.
     * 
     * @param \Gateway\DataSource\Categories $ds
     * @param \SimpleXmlElement $xml
     * @param \Gateway\DataSource\Entity\Category $category
     * @param string $lang
     */
    protected function processChildren($ds, $xml, $category) {
    	$children = array();
		
        $childrenCategoriesPattern = '//categories[@parent_id=' . $category['external_shop_category_id'] . ']'; 
        $childrenCategories = $xml->xpath($childrenCategoriesPattern);

        // add only when leaf because leaf holds all parents path
        if (!count($childrenCategories)) {
        	return null;
            //Utils::log(sprintf("Adding '%s' (ID = %s) under '%s'.", $category->name, $category->id, $category->path));
            
            //$ds->add($category);
        } else { // go deeper in structure until leaf is found
            foreach ($childrenCategories as $childNode) {
                //$child = $this->createCategoryBase($childNode);
                //$child->parent = $category;
                
                $children[] = $this->processCategoryLocalization($ds, $xml, $childNode, $category);
                //$this->processChildren($ds, $xml, $child);
            }
        }
		
		return $children;
    }
	
	
	function loadProducts($xml, $category) {
		return;
	}
    
    /**
     * Searches for products assigned to category.
     * 
     * @param \Gateway\DataSource\Entity\Category $category
     */
     protected function loadAssignedProducts($xml, $category) {
     	$productsCategoriesPattern = '//products_to_categories[@categories_id= ' . $category['external_shop_category_id'] .  ']/@products_id'; 
        $productsCategories = $xml->xpath($productsCategoriesPattern);
        $products = array();
        
        if (is_array($productsCategories) && count($productsCategories)) {
        	/*
            array_walk($productsCategories, function(&$item) { 
                    $item = (string) $item;
            });
			*/
			
            // build multiple values for contains
            // $containsArr = array();
            
            foreach ($productsCategories as $productCategory) {
            	$products[] = (int) $productCategory['products_id'];
                //$containsArr[] = "@products_id=" . $productCategory;
            }
            
			/*
            if (count($containsArr)) {
                $productsPattern = "//products[" . implode(' or ', $containsArr) . "]/@products_model";
                $products = $xml->xpath($productsPattern);
            }
			*/ 
        }
        
        return $products;
    }
	 
	 /*
    protected function loadAssignedProducts($xml, $category) {
        $productsCategoriesPattern = '//products_to_categories[@categories_id= ' . $category->id .  ']/@products_id'; 
        $productsCategories = $xml->xpath($productsCategoriesPattern);
        $products = array();
        
        if (is_array($productsCategories) && count($productsCategories)) {
            array_walk($productsCategories, function(&$item) { 
                    $item = (string) $item;
            });

            // build multiple values for contains
            $containsArr = array();
            
            foreach ($productsCategories as $productCategory) { 
                $containsArr[] = "@products_id=" . $productCategory;
            }
            
            if (count($containsArr)) {
                $productsPattern = "//products[" . implode(' or ', $containsArr) . "]/@products_model";
                $products = $xml->xpath($productsPattern);
            }
        }
        
        return $products;
    }
    */
    
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

            // very simple validation - just search for customers inside
            if (!count($xml->categories)) {
                Utils::log("Invalid input format. XML does not contain required 'categories' element.");
                throw new \Nette\IOException("Invalid input format. XML does not contain required 'customers' element.");
            }
              
        } 
        
        return $xml;  
    }
    
}