<?php
	namespace Rakuten\Handlers;
	
	use Rakuten;
	
	class Orders extends \Rakuten\Rakuten {
		var $existing_orders;
		var $errors;
		var $from_xml_attributes;
		var $group = "orders";
		var $orders_page_limit = 100;
		
		public $error;
		
		function __construct(){
			parent::__construct();	
			
			$this->from_xml_attributes = array(
			    "order_no" => "orders_id",
				"payment" => "payment_class", //payment_class, payment_method, paymentType (site imaat ista vrednost, izgleda ne e bitno od kaj ke ja zemis, eventualno ako treba da proveris ako "payment_class" e prazna, pa da zemis od drugite...)
				"status" => "orders_status",
				"comment_client" => "comments",
				"created" => "date_purchased",
				
				"client~client_id" => "customers_id",
				"client~gender" => "customers_gender",
				"client~first_name" => "customers_firstname",
				"client~last_name" => "customers_lastname",
				"client~company" => "customers_company",
				"client~street" => "customers_street_address",    //
				"client~street_no" => "customers_street_address", // tuka najverojatno treba da parsiras po space od "customers_street_address" 
				"client~zip_code" => "customers_postcode",
				"client~city" => "customers_city",
				"client~country" => "customers_country",
				"client~email" => "customers_email_address",
				"client~phone" => "customers_telephone",
				
				"delivery_address~gender" => "delivery_gender",  //(if "Herr" then "m", if "Frau" then "f")
				"delivery_address~first_name" => "delivery_firstname",
				"delivery_address~last_name" => "delivery_lastname",
				"delivery_address~company" => "delivery_company",
				"delivery_address~street" => "delivery_street_address",     //
				"delivery_address~street_no" => "delivery_street_address",  // tuka najverojatno treba da parsiras po space od "customers_street_address" 
				"delivery_address~zip_code" => "delivery_postcode",
				"delivery_address~city" => "delivery_city",
				"delivery_address~country" => "delivery_country",
				
				"items~item~item_id" => "orders_products_id",
				"items~item~product_id" => "products_id",
				"items~item~product_art_no" => "products_model",  //(we need ETRON number)
				"items~item~name" => "products_name",      // 
				"items~item~name_add" => "products_name",  // tuka najverojatno treba da parsiras po space vo "products_name" 
				"items~item~qty" => "products_quantity",
				"items~item~price" => "products_price",
				"items~item~price_sum" => "final_price",
				"items~item~tax" => "products_tax",	
				"EUR" => "currency"  //proveri dali e vaka (currency="EUR" - always, since is a german market)
			);
			
			$this->errors = array(
				3205 => 'The specified search field is not known',
				3206 => 'The specified collation is not known',
				3207 => 'To a search parameter, an invalid value was passed.',
				3210 => 'The search box to search was not passed',
				3211 => 'The search for the search box is not passed'
			);
			
		}

		function getOrders(){
			$method = 'getOrders';
			$orders_array = array();
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$page = 1;
			$post = 0;
			
			$response = parent::doRequest($url, $post, array(
				'key' => $this->key, 
				'page' => $page, 
				'per_page' => $this->orders_page_limit
			));
			
			if ($response['success'] == 1){
				if (isset($response['orders']['order']) && is_array($response['orders']['order']) && sizeof($response['orders']['order'])){
					if ($response['orders']['paging']['total'] == 1)
						$orders_array[] = $response['orders']['order'];
					else {
						foreach ($response['orders']['order'] as $order)
							$orders_array[] = $order;
							
						if ($response['orders']['paging']['pages'] > 1){
							$page = 2;
							
							while ($page <= $response['orders']['paging']['pages']){
								$response_other = parent::doRequest($url, $post, array(
									'key' => $this->key, 
									'page' => $page, 
									'per_page' => $this->orders_page_limit
								));
								
								$page++;
								
								foreach ($response_other['orders']['order'] as $order)
									$orders_array[] = $order;
							}
						}	
					}					
				}
			}
			
			
			$this->prepareOrders($orders_array);
			$this->existing_orders = $orders_array;
			
			return $orders_array;
		}

		function prepareOrders($orders){
			$params_array = array_flip($this->from_xml_attributes);
			$new_orders_array = array();
			
			foreach ($orders as $order){
				$new_order = array();
				
				foreach ($params_array as $key => $value){					
					if (!strstr($value, '~')){
						$new_order[$key] = $order[$value];
					}
					else {
						$depth = explode('~', $value);
					}
				}

				$new_orders_array[] = $new_order;
			}
			
			print_r($new_orders_array);
			die;
		}
		
		function getErrorMessage($response){
			if ($response['success'] == 0)
				return 'No error, just no results';
			//echo "Response: " . print_r($response) . "<hr />";
			
			$error_message = 'An error occured...';
			//return "an error occured";
			try {
				//print_r($response['errors']['error']);
				$errors_array = array();
				if (!isset($response['errors']['error']['code'])){
					foreach ($response['errors']['error'] as $error)
						$errors_array[] = $error['message'];
					
					$error_message = implode(', ', $errors_array);
				}
				else {
					$error_message = (isset($this->errors[$response['errors']['error']['code']]) ? (isset($this->errors[$response['errors']['error']['code']]) ? $this->errors[$response['errors']['error']['code']] : "Some error occured") : $response['errors']['error']['message']);	
				}				
			}
			catch (exception $ex){
				
			}
			
			Utils::log("Api call error: ". $error_message);
			return $error_message;
		}
		
		function getError(){
			return $this->error;
		}
	}
?>