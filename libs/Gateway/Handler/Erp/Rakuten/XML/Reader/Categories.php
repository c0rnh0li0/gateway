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

    protected $expectedOptions = array('loadProducts');
    
     /**
     * Converts XML to DataSource.
     * 
     */
    public function processXml($xml) {
        $ds = new \Gateway\DataSource\Categories();
        
        Utils::log("Processing categories XML input...");
 
        // 1) search for root
        $rootCategoriesPattern = '//categories[@parent_id=0]';
        $rootCategories = $xml->xpath($rootCategoriesPattern);
        
        // no root => inconsistency
        if (!count($rootCategories)) {
            $msg = "Categories in XML are inconsistent - missing root categories having parent_id=0.";
            
            Utils::log($msg);
            throw new \Nette\InvalidStateException($msg);
        }
        
        // process deep search for children to build up the structure
        foreach ($rootCategories as $node) {
            
            //$category = $this->createCategoryBase($node);
            $this->processCategoryLocalization($ds, $xml, $node);
        }
        
        Utils::log("%s categories has been parsed.", $ds->count());
        
        return $ds;
    }
    
    /**
     * Creates base category information.
     * 
     * @param \SimpleXmlElement $node
     * @return \Gateway\DataSource\Entity\Category
     */
    protected function createCategoryBase($xml, $node) {
        $category = new Category();
        $category->id = (int) $node['categories_id'];
        $category->isActive = ((int) $node['categories_status']) > 0 ? true : false;
        $category->isVisible = ((int) $node['categories_visibility']) > 0 ? true : false; 
        $category->addSpecialProperty('isAnchor', false);
        $category->addSpecialProperty('priority', (int) $node['sort_order']);
        
        $image = (string) $node['categories_image'];
        $category->image = $image ? $image : null;

        // if loadProducts specified, we parse also products to categories assignments
        if ($this->options->getData('loadProducts')) {
            // load assigned products
            $products = $this->loadAssignedProducts($xml, $category);
            
            $category->addSpecialProperty('products', implode(',', $products));          
        }
        
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
        
        if ($parent) {
            $category->parent = $parent;
        }
        
        $xmlCategoriesDescriptionPattern = '//categories_description[@categories_id=' . $category->id . ']';       
        $xmlCategoriesDescription = $xml->xpath($xmlCategoriesDescriptionPattern);
       
        // localized values
        foreach ($xmlCategoriesDescription as $xmlCategoryDescription) {
            $localized = new \Gateway\DataSource\Entity\Category\LocalizedInfo();
            
            $localized->lang = isset($xmlCategoryDescription['language_id']) ? (string) $xmlCategoryDescription['language_id'] : Localized::NOT_LOCALIZED;
            $localized->name = (string) $xmlCategoryDescription['categories_name'];
            $localized->description = (string) $xmlCategoryDescription['categories_description'];
            
            $category->addLocalizedInfo($localized);
        }
        
        // continue deeper
        $this->processChildren($ds, $xml, $category);
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
        $childrenCategoriesPattern = '//categories[@parent_id=' . $category->id . ']'; 
        $childrenCategories = $xml->xpath($childrenCategoriesPattern);

        // add only when leaf because leaf holds all parents path
        if (!count($childrenCategories)) {
            Utils::log(sprintf("Adding '%s' (ID = %s) under '%s'.", $category->name, $category->id, $category->path));
            
            $ds->add($category);
        } else { // go deeper in structure until leaf is found
            foreach ($childrenCategories as $childNode) {
                //$child = $this->createCategoryBase($childNode);
                //$child->parent = $category;
                
                $this->processCategoryLocalization($ds, $xml, $childNode, $category);
                //$this->processChildren($ds, $xml, $child);
            }
        }
    }
    
    /**
     * Searches for products assigned to category.
     * 
     * @param \Gateway\DataSource\Entity\Category $category
     */
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