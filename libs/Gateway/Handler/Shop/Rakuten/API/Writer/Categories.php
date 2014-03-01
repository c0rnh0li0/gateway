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
		
		$domain = $this->options->get('domain');
        $key = $this->options->get('key');
		
		$this->Categories = new \Rakuten\Handlers\Categories($key, $domain);
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
		
		//echo "dump in " . $_SERVER['DOCUMENT_ROOT'] . '/existing_categories.txt<br />';
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/existing_categories.txt', print_r($this->existing_categories, true));
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

		//echo "dump in " . $_SERVER['DOCUMENT_ROOT'] . '/etron_categories.txt<br />';
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/etron_categories.txt', print_r($this->dataSource, true));
		
        Utils::log("Processing DataSource to Rakuten via Api...");
               
        try {
        	// init current magento state
            $this->init();
			
			$this->prepareCategories();
			
			$this->deleteMissingCategories();
			
			$this->updateExistingCategories();
			
			$this->addNewCategories();			
        } catch (\SoapFault $e) {
            Utils::log(sprintf("Error during passing via Rakuten API: %s (%s)", $e->getMessage(), $e->getCode()));

            throw $e;
        }

		$processed_categories = count($this->categories_to_add) + count($this->categories_to_update) + count($this->categories_to_delete);
        Utils::log(sprintf("Finished with total of %s categories, %s created/updated.", count($this->dataSource), $processed_categories));

        return ($processed_categories);
    }

	function prepareCategories(){
		// check if anything is there to be deleted
		foreach ($this->existing_categories as $shop_category){
			if (!isset($shop_category['external_shop_category_id']) || $shop_category['external_shop_category_id'] == '')
				continue;
			
			if (!$this->isInNewCategories($shop_category['external_shop_category_id']))
				$this->categories_to_delete[] = $shop_category;
		}
		
		// check which categories to edit and which to add as new
		foreach ($this->dataSource as $etron_category){
			if ($this->isInStoreCategories($etron_category['external_shop_category_id']))
				$this->categories_to_update[] = $etron_category;
			else 
				$this->categories_to_add[] = $etron_category;
		}
	}
	
	function addNewCategories(){
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
		for ($i = 0; $i < count($this->categories_to_update); $i++){
			$existing_category = $this->getExistingCategory($this->categories_to_update[$i]['external_shop_category_id']);
			
			if ($this->isCategoryChanged($existing_category, $this->categories_to_update[$i])){
				foreach ($existing_category as $key => $value){
					if (isset($this->categories_to_update[$i][$key]) && $this->categories_to_update[$i][$key] != '')
						$existing_category[$key] = $this->categories_to_update[$i][$key];						
				}
				
				$this->categories_to_update[$i] = $existing_category;
			}
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
	
	function deleteMissingCategories(){
		if (sizeof($this->categories_to_delete)){
			foreach ($this->categories_to_delete as $category_to_delete)
				if (!$this->Categories->deleteCategory($category_to_delete['external_shop_category_id']))
					Utils::log("Error [Categories Delete]: %s.", $this->Categories->getError());
		}
			
		Utils::log("DELETE [Categories]: %s.", count($this->categories_to_delete));
		return true;
	}
	
	
	
	// Helper methods from here on
	function isInNewCategories($external_shop_category_id){
		foreach ($this->dataSource as $etron_category){
			if ($etron_category['external_shop_category_id'] == $external_shop_category_id)
				return true;	
		}
		
		return false;
	}
	
	function isInStoreCategories($external_shop_category_id){
		foreach ($this->existing_categories as $shop_category){
			if ($shop_category['external_shop_category_id'] == $external_shop_category_id)
				return true;
		}
		
		return false;
	}

	function getExistingCategory($external_shop_category_id){
		foreach ($this->existing_categories as $existing_category)
			if ($existing_category['external_shop_category_id'] == $external_shop_category_id)
				return $existing_category;
		
		return false;
	}
	
	function isCategoryChanged($existing_category, $potential_update_category){
		$compare_columns = array_keys($this->Categories->category_compare);
		
		$is_category_changed = false;
		foreach ($compare_columns as $column){
			if (!isset($existing_category[$column])){
				$is_category_changed = true;
				break;
			}
			
			if (isset($potential_update_category[$column]) && $potential_update_category[$column] != '' && $existing_category[$column] != $potential_update_category[$column]) {
				$is_category_changed = true;
				break;	
			}				
		} 
	
		return $is_category_changed;
	}
}