<?php

namespace Gateway\Handler\Shop\Rakuten\API\Writer;

use Gateway\IHandler,
	Gateway\Utils,
	Gateway\DataSource\Entity\Category, 
	Rakuten\Handlers;

require_once('../libs/Rakuten/Handlers/Products.php');

/**
 * Orders DataSource to API interface handler.
 * 
 * @author Darko Krstev
 */
class Products extends \Gateway\Handler\Shop\Rakuten\API\Writer {

    protected $type = IHandler::TYPE_PRODUCTS;
    
	protected $Products;
	public $existing_products;
	
	public $products_to_update;
	public $products_to_add;
	public $products_to_delete;
	
    /**
     * Number of processed skus.
     * 
     * @var int
     */        
    protected $processed = 0;

    /**
     * Passes DataSource to API.
     * 
     * @return int Number of processed items.
     */
    public function process() {
    	Utils::log(">>> " . get_class($this) . " <<<");

		$this->Products = new \Rakuten\Handlers\Products();
    	
        // FIXME check when setting datasource
        if (!$this->dataSource) {
            Utils::log("No datasource given. Skipping...");
            return;
        }
		
		echo "dump in " . $_SERVER['DOCUMENT_ROOT'] . '/products.txt<br />';
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/products.txt', print_r($this->dataSource, true));
		
		//echo "<br /><br /><br /><br /><br /><br /><hr />Datasource products: " .print_r($this->$this->dataSource, true);
		
		$this->init();
		
		

		


		die;



        return $allcalls; //$this->processed;
    }
	
	function init(){
		$this->existing_products = $this->Products->getProducts();
		
		echo "dump in " . $_SERVER['DOCUMENT_ROOT'] . '/existing_products.txt<br />';
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/existing_products.txt', print_r($this->existing_products, true));
		//echo "<br /><br /><br /><br /><br /><br /><hr />Existing products: " .print_r($this->existing_products, true);
		
		$this->deleteMissingProducts();
			
		$this->updateExistingProducts();
		
		$this->addNewProducts();
	}
	
	
	function addNewProducts(){
		//$new_categories = array();
		
		$old_products = array();
		foreach ($this->existing_products as $old)
			$old_products[] = $old['product_art_no'];
		
		
		foreach ($this->dataSource as $new){
			if (!in_array($new['product_art_no'], $old_products) && (int)$new['product_id'] > 0)			
				$this->products_to_add[] = $new;	
		}
		
		if (sizeof($this->products_to_add)){
			foreach ($this->products_to_add as $product_to_add){				
				if (!$this->Products->addProduct($product_to_add))
					Utils::log("Error [Products Add]: %s.", $this->Products->getError());
			}
		}
		
		Utils::log("ADD [Products]: %s.", count($this->products_to_add));
		
		return true;
	}
	
	function deleteMissingProducts(){
		// $this->dataSource -> new ones from XML
		// $this->existing_categories -> old ones fetched from the API
		
		foreach ($this->dataSource as $new){
			if ($this->isInStore($new['product_art_no']) && (int)$new['product_id'] < 0)			
				$this->products_to_delete[] = $new;	
		}
		
		/*
		foreach ($this->existing_products as $existing_product){
			if (!$this->isInStore($existing_product['product_art_no']) && !empty($existing_product['product_art_no']))
				$this->products_to_delete[] = $existing_product;
		}	
		*/
		
		if (sizeof($this->products_to_delete)){
			foreach ($this->products_to_delete as $product_to_delete)
				if (!$this->Products->deleteProduct($product_to_delete))
					Utils::log("Error [Products Delete]: %s.", $this->Products->getError());
		}
			
		Utils::log("DELETE [Products]: %s.", count($this->products_to_delete));
		return true;
	}
			
	function updateExistingProducts(){
		foreach ($this->existing_products as $existing_product){
			$updated_product = $this->isInStore($existing_product['product_art_no']);
			
			if ($updated_product !== false)
				$this->isProductChanged($existing_product, $updated_product);
		}
		
		if (sizeof($this->products_to_update)){
			foreach ($this->products_to_update as $product_to_update){
				if (!$this->Products->editProduct($product_to_update))
					Utils::log("Error [Categories Update]: %s.", $this->Products->getError());
			}
		}
		
		Utils::log("EDIT [Products]: %s.", count($this->products_to_update));
		
		return true;
	}
		
	function isProductChanged($existing_product, $updated_product){
		$compare_columns = array_keys($this->Products->product_compare);
		
		foreach ($compare_columns as $column){
			if ($existing_product[$column] !== $updated_product[$column]) {
				foreach ($existing_product as $key => $value){
					if (isset($updated_product[$key]) && $updated_product[$key] !== '')
						$existing_product[$key] = $updated_product[$key];
				}
				
				$this->products_to_update[] = $existing_product;
				break;
			}				
		} 
	}
	
	function isInStore($product_art_id){
		foreach ($this->dataSource as $new_product)
			if ($new_product['product_art_no'] == $product_art_id) 
				return $new_product;
				
		return false;
	}
	
	function isNew($product_art_id){
		foreach ($this->dataSource as $new_product)
			if ($new_product['product_art_no'] != $product_art_id) 
				return $new_product;
				
		return false;
	}

}

