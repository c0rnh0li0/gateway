<?php

namespace Gateway\Handler\Shop\Rakuten\API\Writer;

use Gateway\IHandler,
	Gateway\Utils,
	Gateway\DataSource\Entity\Product, 
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
		
		
		// $attrs = $product->getAttributes($lang); ????????????????????????????????????????????????????
		


		die;



        return $allcalls; //$this->processed;
    }
	
	function init(){
		$this->existing_products = $this->Products->getProducts();
		
		echo "dump in " . $_SERVER['DOCUMENT_ROOT'] . '/existing_products.txt<br />';
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/existing_products.txt', print_r($this->existing_products, true));
		//echo "<br /><br /><br /><br /><br /><br /><hr />Existing products: " .print_r($this->existing_products, true);
		
		$this->prepareProducts();
		
		
		$this->deleteMissingProducts();
			
		$this->updateExistingProducts();
		
		$this->addNewProducts();
	}
	
	function prepareProducts(){
		/*
		$product_nette = new \Gateway\Handler\Erp\Rakuten\XML\Reader\Products();
		$valid_attributes = $product_nette->getAttributes(0);
		echo '$valid_attributes: <br />';
		var_dump($valid_attributes);
		*/
				
		// check if anything is there to be deleted
		$count = 0;
		foreach ($this->existing_products as $shop_product){
			if (!isset($shop_product['product_art_no']) || $shop_product['product_art_no'] == '')
				continue;
			
			if (!$this->isInNewProducts($shop_product['product_art_no']))
				$this->products_to_delete[] = $shop_product;
		}
		
		// check which products to edit and which to add as new
		foreach ($this->dataSource as $etron_product){
			if ($this->isInStoreProducts($etron_product['product_art_no']))
				$this->products_to_update[] = $etron_product;
			else 
				$this->products_to_add[] = $etron_product;
		}
		
		
		//echo "<h1>Add products:</h1> " . print_r($this->products_to_add, true) . "<br /><br /><br /><br />";
		//echo "<h1>edit products:</h1> " . print_r($this->products_to_update, true) . "<br /><br /><br /><br />";
		//echo "<h1>delete products:</h1> " . print_r($this->products_to_delete, true) . "<br /><br /><br /><br />";
		//echo "<hr>";
	}
	
	function isInNewProducts($product_art_no){
		foreach ($this->dataSource as $etron_product){
			if ($etron_product['product_art_no'] == $product_art_no)
				return true;	
		}
		
		return false;
	}
	
	function isInStoreProducts($product_art_no){
		foreach ($this->existing_products as $shop_product){
			if ($shop_product['product_art_no'] == $product_art_no)
				return true;
		}
		
		return false;
	}
	
	function getStoreProduct($product_art_no){
		foreach ($this->existing_products as $shop_product){
			if ($shop_product['product_art_no'] == $product_art_no)
				return $shop_product;
		}
		
		return false;
	}
	
	
	function updateExistingProducts(){
		if (sizeof($this->products_to_update)){
			$count = 0;
			foreach ($this->products_to_update as $product_to_update){
				$existing_product = $this->getStoreProduct($product_to_update['product_art_no']);
				
				if (!$this->isProductChanged($existing_product, $product_to_update))
					unset($this->products_to_update[$count]);
				else {
					if (isset($existing_product['images']) && is_array($existing_product['images']))
						$this->products_to_update[$count]['existing_images'] = $existing_product['images'];
					
					if (isset($existing_product['attributes']) && is_array($existing_product['attributes']))
						$this->products_to_update[$count]['existing_attributes'] = $existing_product['attributes'];
					
					if (isset($existing_product['variants']) && is_array($existing_product['variants']))
						$this->products_to_update[$count]['existing_variants'] = $existing_product['variants'];
					
					if (isset($existing_product['categories']) && is_array($existing_product['categories']))
						$this->products_to_update[$count]['existing_categories'] = $existing_product['categories'];	
				}
				
				$count++;
			}
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
	
	
	function addNewProducts(){
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
		if (sizeof($this->products_to_delete)){
			foreach ($this->products_to_delete as $product_to_delete)
				if (!$this->Products->deleteProduct($product_to_delete))
					Utils::log("Error [Products Delete]: %s.", $this->Products->getError());
		}
			
		Utils::log("DELETE [Products]: %s.", count($this->products_to_delete));
		return true;
	}
		
	function isProductChanged($existing_product, $updated_product){
		$compare_columns = $this->Products->product_compare;
		
		foreach ($compare_columns as $column => $keys){
			if (!is_array($keys)) {
				if (isset($updated_product[$keys]) && $updated_product[$keys] != '' &&  
					isset($existing_product[$keys]) && $existing_product[$keys] != $updated_product[$keys]){
						echo "the difference: {$existing_product[$keys]} != {$updated_product[$keys]}<br />";
						
						return true;	
				}					
			}
		}
		
		if ($this->isProductAttributesChanged($existing_product, $updated_product)){
			echo "product attributes are changed<br />";
			return true;
		}
		
		if ($this->isProductCategoriesChanged($existing_product, $updated_product)){
			echo "product categories are changed<br />";
			return true;
		}
		
		if ($this->isProductImagesChanged($existing_product, $updated_product)){
			echo "product images are changed<br />";
			return true;
		}
			
		
		if ($this->isProductVariantsChanged($existing_product, $updated_product)){
			echo "product variants are changed<br />";
			return true;
		}
			
		
		return false;
	}

	function isProductAttributesChanged($existing_product, $updated_product){
		$column = 'attributes';
		
		if (!isset($updated_product[$column]) || !sizeof($updated_product[$column])){
			echo "updated product has no attributes<br />";
			return false;
		}
			
		
		if ((!isset($existing_product[$column]) || !sizeof($existing_product[$column])) && 
			(isset($updated_product[$column])) && sizeof($updated_product[$column])){
			echo "existing product has no attributes<br />";
				
			return true;	
		}
			
			
		for ($i = 0; $i < count($updated_product[$column]); $i++){
			if ($updated_product[$column][$i]['name'] == '' || $updated_product[$column][$i]['value'] == '')
				unset($updated_product[$column][$i]); 
		}
		
		$attribute_exists = false;
		foreach ($updated_product[$column] as $attribute){			
			foreach ($existing_product[$column] as $existing_attribute){
				if ($attribute['name'] == $existing_attribute['name']){
					$attribute_exists = true;
					
					if ($attribute['value'] != $existing_attribute['value']){
						echo "attr diff: {$attribute['value']} != {$existing_attribute['value']}<br />";
						return true;	
					}
				}
			}	

			if (!$attribute_exists){
				echo "attr dif: attribute is new<br />";
				return true;
			}
		}
		
		
		return false;
	}


	function isProductCategoriesChanged($existing_product, $updated_product){
		$column = 'categories';
		
		if (!isset($updated_product[$column]) || !sizeof($updated_product[$column])){
			echo "updated product has no categories<br />";
			return false;
		}
		
		
		if ((!isset($existing_product[$column]) || !sizeof($existing_product[$column])) && 
			(isset($updated_product[$column])) && sizeof($updated_product[$column])){
			echo "existing product has no categories<br />";
			return true;
		}
			
		if (sizeof($existing_product[$column]) != sizeof($updated_product[$column])){
			echo "categories count is different<br />";
			return true;
		}	
			
		foreach ($updated_product[$column] as $category){
			if (!in_array($category, $existing_product[$column])){
				echo "category not in categories array<br />";
				return true;
			}
		}
		
		return false;
	}
	
	function isProductVariantsChanged($existing_product, $updated_product){
		$column = 'variants';
		
		if (!isset($updated_product[$column]) || !sizeof($updated_product[$column])){
			echo "updated product has no variants<br />";
			return false;		
		}
		
		if ((!isset($existing_product[$column]) || !sizeof($existing_product[$column])) && 
			(isset($updated_product[$column])) && sizeof($updated_product[$column])){
			echo "existing product has no variants<br />";
			return true;
		}
			
		if (sizeof($existing_product[$column]) != sizeof($updated_product[$column])){
			echo "number of variants is different<br />";
			return true;
		}
		
		foreach ($updated_product[$column] as $variant){
			foreach ($existing_product['variants'] as $existing_variant){
				if ($existing_variant['variant_art_no'] == $variant['variant_art_no']){
					foreach ($this->Products->product_compare['variants'] as $variant_column){
						if ($existing_variant[$variant_column] != $variant[$variant_column]){
							echo "variants different at: $variant_column: {$existing_variant[$variant_column]} != {$variant[$variant_column]}<br />";
							return true;
						}
					}
				}
			}
		}
	
		
		return false;
	}
	
	
	function isProductImagesChanged($existing_product, $updated_product){
		$column = 'images';
		
		if (!isset($updated_product[$column]) || !sizeof($updated_product[$column])){
			echo "updated product has no images<br />";
			return false;
		}
		
		if ((!isset($existing_product[$column]) || !sizeof($existing_product[$column])) && 
			(isset($updated_product[$column])) && sizeof($updated_product[$column])){
			echo "existing product has no images<br />";
			return true;			
		}
			
		foreach ($updated_product[$column] as $image){
			$first_url_string = substr($image, 0, 1);
			
			echo "image first char: $first_url_string<br />";
			
			if ($first_url_string == '+' || $first_url_string == '-'){
				echo "updated product has changed images<br />";
				return true;
			}
		}
		
		return false;		
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

