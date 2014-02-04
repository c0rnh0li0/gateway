<?php
	namespace Rakuten\Handlers;
	
	use Rakuten;
	
	class Categories extends \Rakuten\Rakuten {
		var $category;
		var $category_errors;
		var $errors;
		var $category_from_xml;
		var $from_attributes;
		var $category_response;
		var $group = "categories";
		var $categories_page_limit = 100;
		
		public $error;
		
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
			
			$this->errors = array(
				3905 => 'The parent-Shop Category was not found',
				3910 => 'The external store category ID is already taken',
				3915 => 'The "url" parameter has an invalid value', 
				4105 => 'The Shop Category ID or the external store category ID is not passed',
				4110 => 'The Shop Category was not found',
				4115 => 'The parent-Shop Category was not found',
				4120 => 'The "url" parameter has an invalid value', 
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
			
			
			$this->category_compare = array(
				'external_shop_category_id' => '',				// String (20): ID of the Shop Category from an external system																
				'external_parent_shop_category_id' => '', 		// String (20): ID of the parent-shop category from an external system
				'name' => '',									// String: Name of Shop Category
				'description' => '',							// String: Description of Shop Category
				'layout' => '',									// String: Product representation in the Shop Category
				'product_order' => '',							// String: Sorting of products in the Shop Category
				'visible' => '',								// Integer: Visibility of the Shop Category
				'position' => '', 								// Integer: Position of the category in the category-level
				'url' => '', 									// String: URL to the category in the shop
				'meta_title' => '', 							// String: Page Title (SEO) of the Shop Category
				//'meta_description' => '', 						// String: Meta-Description (SEO) of the Shop Category
				//'meta_keywords' => ''							// String: Keywords (SEO) of the category page
			);
		}

		function prepareCategory($new_category){
			$prepared_data = array();
			
			foreach ($new_category as $key => $data)
				$prepared_data[$key] = $data;
			
			if ($prepared_data['shop_category_id'] == '')
				unset($prepared_data['shop_category_id']);
			if ($prepared_data['parent_shop_category_id'] == '')
				$prepared_data['parent_shop_category_id'] = 0;
			if ($prepared_data['layout'] == '')
				$prepared_data['layout'] = 'products';
			if ($prepared_data['product_order'] == '')
				$prepared_data['product_order'] = 'manual';
			
			return $prepared_data;
		}
		
		function addCategory($category_data){
			$method = 'addShopCategory';
			$post = 1;
			
			$category = $this->prepareCategory($category_data);
			
			$request_params = $category;
			$request_params['key'] = $this->key; 
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}
		
		function editCategory($category_data){
			$method = 'editShopCategory';
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$category = $this->prepareCategory($category_data);
			
			$request_params = $category;
			$request_params['key'] = $this->key; 
			//$request_params['external_shop_category_id'] = $external_shop_category_id;
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false;
		}
		
		function deleteCategory($external_shop_category_id){
			$method = 'deleteShopCategory';
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$result = parent::doRequest($url, $post, array(
				'key' => $this->key, 
				'external_shop_category_id' => $external_shop_category_id, 
				//'shop_category_id ' => ''
			));
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}
		
		function getExistingCategories(){
			$method = 'getShopCategories';
			$categories_array = array();
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$page = 1;
			$post = 0;
			
			$response = parent::doRequest($url, $post, array(
				'key' => $this->key, 
				'page' => $page, 
				'per_page' => $this->categories_page_limit
			));
			
			if ($response['success'] == 1){
				foreach ($response['categories']['category'] as $category)
					$categories_array[] = $category;
				
				if ($response['categories']['paging']['pages'] > 1){
					$page = 2;
					
					while ($page <= $response['categories']['paging']['pages']){
						$response_other = parent::doRequest($url, $post, array(
							'key' => $this->key, 
							'page' => $page, 
							'per_page' => $this->categories_page_limit
						));
						
						$page++;
						
						foreach ($response_other['categories']['category'] as $category)
							$categories_array[] = $category;
					}
				}
			}
			
			for ($i = 0; $i < count($categories_array); $i++){
				if (is_array($categories_array[$i]['external_parent_shop_category_id']) && sizeof($categories_array[$i]['external_parent_shop_category_id']))
					$categories_array[$i]['external_parent_shop_category_id'] = $categories_array[$i]['external_parent_shop_category_id'][0];
				else
					$categories_array[$i]['external_parent_shop_category_id'] = '';
				
				foreach ($categories_array[$i]['seo'] as $key => $val)
					$categories_array[$i][$key] = $val;
				
				unset($categories_array[$i]['seo']);
			} 
			
			return $categories_array;
		}
		
		function assignProducts(){
			
		}
		
		function unassignProducts(){
			
		}
		
		function getErrorMessage($response){
			return $this->errors[$response['errors']['error']['code']];
		}
		
		function getError(){
			return $this->error;
		}
	}
?>