<?php
	namespace Rakuten\Handlers;
	
	use Rakuten;
	
	class Categories extends \Rakuten\Rakuten {
		var $category;
		var $category_errors;
		var $category_from_xml;
		var $from_attributes;
		
		var $category_response;
		
		var $group = "categories";
		
		var $categories_page_limit = 100;
		
		
		function __construct(){
			parent::__construct();	
			
			$this->category = array(
				'shop_category_id' => '',						// Integer: ID of the Shop Category
																// Rules: Either the parameter shop_category_id or external_shop_category_id is required.
				
				'external_shop_category_id' => '',				// String (20): ID of the Shop Category from an external system																
				'parent_shop_category_id' => '', 				// Integer: ID of the parent-Shop Category
				'external_parent_shop_category_id' => '', 		// String (20): ID of the parent-shop category from an external system
				'name' => '',									// String: Name of Shop Category
				'description' => '',							// String: Description of Shop Category
				
				'layout' => '',									// String: Product representation in the Shop Category
																// Rules:	
																// products = Products from this category
																// child_products = Products from this category and subcategories
																// no_products = No products, only view description
																
				'product_order' => '',							// String: Sorting of products in the Shop Category
																// Rules:	
																// manual = Manual sorting
																// Popularity = Popularity
																// price_asc = Price (low to high)
																// price_desc = Price (high to low)
																// name_asc = Name (ascending)
																// name_desc = Name (descending)
																// created_desc = Created Date (descending)
																// art_no_asc = Item Number (Low to High)
																// art_no_desc = Item Number (descending)
																
				'visible' => '',								// Integer: Visibility of the Shop Category
																// Rules:	
																// 0 = Hidden in the category tree
																// 1 = Displayed in the category tree
																// -1 = Disabled
																
				'position' => '', 								// Integer: Position of the category in the category-level
				'url' => '', 									// String: URL to the category in the shop
				'meta_title' => '', 							// String: Page Title (SEO) of the Shop Category
				'meta_description' => '', 						// String: Meta-Description (SEO) of the Shop Category
				'meta_keywords' => ''							// String: Keywords (SEO) of the category page
			);
			
			$this->category_response = array(
				'success' => 1,
				'shop_category_id' => ''
			);
			
			$this->category_add_errors = array(
				3905 => 'The parent-Shop Category was not found',
				3910 => 'The external store category ID is already taken',
				3915 => 'The "url" parameter has an invalid value'
			);
			
			$this->category_edit_errors = array(
				4105 => 'The Shop Category ID or the external store category ID is not passed',
				4110 => 'The Shop Category was not found',
				4115 => 'The parent-Shop Category was not found',
				4120 => 'The "url" parameter has an invalid value'
			);
			
			$this->category_delete_errors = array(
				4205 => 'The Shop Category ID or the external store category ID is not passed',
				4210 => 'The category could not be found'
			);
			
			
			
			$this->category_from_xml = array(
				'shop_category_id' => '',						// Integer: ID of the Shop Category
																// Rules: Either the parameter shop_category_id or external_shop_category_id is required.
				
				'external_shop_category_id' => '',				// String (20): ID of the Shop Category from an external system																
				'parent_shop_category_id' => '', 				// Integer: ID of the parent-Shop Category
				'external_parent_shop_category_id' => '', 		// String (20): ID of the parent-shop category from an external system
				'name' => '',									// String: Name of Shop Category
				'description' => '',							// String: Description of Shop Category
				
				'layout' => '',									// String: Product representation in the Shop Category
																// Rules:	
																// products = Products from this category
																// child_products = Products from this category and subcategories
																// no_products = No products, only view description
																
				'product_order' => '',							// String: Sorting of products in the Shop Category
																// Rules:	
																// manual = Manual sorting
																// Popularity = Popularity
																// price_asc = Price (low to high)
																// price_desc = Price (high to low)
																// name_asc = Name (ascending)
																// name_desc = Name (descending)
																// created_desc = Created Date (descending)
																// art_no_asc = Item Number (Low to High)
																// art_no_desc = Item Number (descending)
																
				'visible' => '',								// Integer: Visibility of the Shop Category
																// Rules:	
																// 0 = Hidden in the category tree
																// 1 = Displayed in the category tree
																// -1 = Disabled
																
				'position' => '', 								// Integer: Position of the category in the category-level
				'url' => '', 									// String: URL to the category in the shop
				'meta_title' => '', 							// String: Page Title (SEO) of the Shop Category
				'meta_description' => '', 						// String: Meta-Description (SEO) of the Shop Category
				'meta_keywords' => ''							// String: Keywords (SEO) of the category page
			);
		}
		
		function addCategory(){
			
		}
		
		function editCategory(){
			
		}
		
		function deleteCategory(){
			
		}
		
		function getCategories(){
			$method = 'getShopCategories';
			$categories_array = array();
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$page = 1;
			
			//while (true){
				$response = $this->doRequest($url, 1, array(
					'key' => $this->key, 
					'page' => $page, 
					'per_page' => $this->categories_page_limit
				));
				echo $response; 
				
				$tmp = $this->parseArray($response);
				
				print_r($tmp);
				die;
			//}
			//$response = $this->doRequest($url, $post = 1, $postfields = array());
			
			//return $this->parseArray($response);
			//$categories_xml = $this->parseXML($response);
			
			
		}
		
		function assignProducts(){
			
		}
		
		function unassignProducts(){
			
		}
		
		
	}
?>