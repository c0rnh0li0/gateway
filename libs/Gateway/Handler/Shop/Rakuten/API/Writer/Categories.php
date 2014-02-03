<?php

namespace Gateway\Handler\Shop\Rakuten\API\Writer;

use Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Category, 
	Rakuten\Handlers;

require_once('../libs/Rakuten/Handlers/Categories.php');

/**
 * Categories DataSource to API interface handler.
 *
 * @author Darko Krstev
 */
class Categories extends \Gateway\Handler\Shop\Rakuten\API\Writer {

    protected $type = IHandler::TYPE_CATEGORIES;
	
	protected $Categories;
	public $existing_categories;
	
	public $categories_to_update;
	public $categories_to_add;
	public $categories_to_delete;

    /**
     * Sets handler options (credentials, settings etc.) and validates them.
     * Overriden due to update expectedOptions stuff.
     * 
     * @param \Gateway\Handler\Options $options
     */
    public function __construct(\Gateway\Handler\Options $options) {
        //$this->expectedOptions[] = 'assignProducts';

        parent::__construct($options);
		
		$this->Categories = new \Rakuten\Handlers\Categories();
    }
	
    /**
     * Datasource tree converted to list.
     * 
     * @var array 
     */
    protected $dataSourceList = array();

    /**
     * Multicall switcher instead of simple calls (can help to speed up process 
     * but very buggy on Magento side - sometimes freezes).
     * 
     * @var bool
     */
    protected $useMulticall = false;

    /**
     * Inits categories structure from Magento to ease following work.
     * 
     */
    protected function init() {
        // API - get existing categories
        $this->existing_categories = $this->Categories->getExistingCategories();
    }

    /**
     * Converts XML to DataSource.
     * 
     * @return int Number of processed items.
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");

        // FIXME check when setting datasource
        if (!$this->dataSource) {
            Utils::log("No datasource given. Skipping...");
            return;
        }


        Utils::log("Processing DataSource to Rakuten via Api...");

        // SCENARIO
        // 1) extract ids from tree
        // 2) get all categories info
        // 3) make comparsion (according to erpId) and merge
        // 4) delete not existing
        // 5) process synchro for updated or new only     
               
        try {
        	// init current magento state
            $this->init();
			
			$this->deleteMissingCategories();
			
			$this->updateExistingCategories();
			
			$this->addNewCategories();			
        } catch (\SoapFault $e) {
            Utils::log(sprintf("Error during passing via Rakuten API: %s (%s)", $e->getMessage(), $e->getCode()));

            throw $e;
        }

        Utils::log(sprintf("Finished with total of %s categories, %s created/updated.", count($this->dataSourceList), $this->categories['processedCount']));

        return (count($this->categories_to_add) + count($this->categories_to_update) + count($this->categories_to_delete));
    }
    
	
	function addNewCategories(){
		$new_categories = array();
		
		$old_categories = array();
		foreach ($this->existing_categories as $old)
			$old_categories[] = $old['external_shop_category_id'];
		
		
		foreach ($this->dataSource as $new){
			if (!in_array($new['external_shop_category_id'], $old_categories))			
				$this->categories_to_add[] = $new;	
		}
		
		if (sizeof($this->categories_to_add)){
			foreach ($this->categories_to_add as $category_to_add){				
				if (!$this->Categories->addCategory($category_to_add))
					Utils::log("Error [Categories Add]: %s.", $this->Categories->getError());
			}
		}
		
		Utils::log("ADD [Categories]: %s.", count($this->categories_to_add));
		
		return true;
	}
	
	function updateExistingCategories(){
		foreach ($this->existing_categories as $existing_category){
			$updated_category = $this->isInStore($existing_category['external_shop_category_id']);
			
			if ($updated_category !== false)
				$this->isCategoryChanged($existing_category, $updated_category);
		}
		
		if (sizeof($this->categories_to_update)){
			foreach ($this->categories_to_update as $category_to_update){
				if (!$this->Categories->editCategory($category_to_update))
					Utils::log("Error [Categories Update]: %s.", $this->Categories->getError());
			}
		}
		
		Utils::log("EDIT [Categories]: %s.", count($this->categories_to_update));
		
		return true;
	}
	
	
	function isCategoryChanged($existing_category, $updated_category){
		$compare_columns = array_keys($this->Categories->category_compare);
		
		foreach ($compare_columns as $column){
			if ($existing_category[$column] !== $updated_category[$column]) {
				foreach ($existing_category as $key => $value){
					if (isset($updated_category[$key]) && $updated_category[$key] !== '')
						$existing_category[$key] = $updated_category[$key];
				}
				
				$this->categories_to_update[] = $existing_category;
				break;
			}				
		} 
	}
	
	
	function deleteMissingCategories(){
		// $this->dataSource -> new ones from XML
		// $this->existing_categories -> old ones fetched from the API
		
		foreach ($this->dataSource as $new){
			if ($this->isInStore($existing_category['external_shop_category_id']) && (int)$new['category_id'] < 0)			
				$this->categories_to_delete[] = $new;	
		}
		
		/*
		foreach ($this->existing_categories as $existing_category){
			if (!$this->isInStore($existing_category['external_shop_category_id']) && !empty($existing_category['external_shop_category_id']))
				$this->categories_to_delete[] = $existing_category;
		}	
		*/
		
		if (sizeof($this->categories_to_delete)){
			foreach ($this->categories_to_delete as $category_to_delete)
				if (!$this->Categories->deleteCategory($category_to_delete['external_shop_category_id']))
					Utils::log("Error [Categories Delete]: %s.", $this->Categories->getError());
		}
			
		Utils::log("DELETE [Categories]: %s.", count($this->categories_to_delete));
		return true;
	}
	
	
	
	function isInStore($category_id){
		foreach ($this->dataSource as $new_category)
			if ($new_category['external_shop_category_id'] == $category_id) 
				return $new_category;
				
		return false;
	}
	
	function isNew($category_id){
		foreach ($this->dataSource as $new_category)
			if ($new_category['external_shop_category_id'] != $category_id) 
				return $new_category;
				
		return false;
	}
}