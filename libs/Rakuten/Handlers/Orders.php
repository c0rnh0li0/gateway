<?php
	namespace Rakuten\Handlers;
	
	use Rakuten, 
		Gateway\Utils;
	
	class Orders extends \Rakuten\Rakuten {
		var $existing_orders;
		var $errors;
		var $from_xml_attributes;
		var $predefined_attributes; 
		var $xml_mappings;
		var $allowed_arrays;
		var $group = "orders";
		var $orders_page_limit = 100;
		var $node_joins;
		var $add_same_nodes;
		var $empty_nodes;
		var $tax_mappings;
		var $orders_response_methods;
		
		public $error;
		
		function __construct($key, $domain){
			parent::__construct($key, $domain);	
			
			$this->from_xml_attributes = array(
			    "order_no" => "orders_id",
				"payment" => "payment_class", // payment_method, paymentType (site imaat ista vrednost, izgleda ne e bitno od kaj ke ja zemis, eventualno ako treba da proveris ako "payment_class" e prazna, pa da zemis od drugite...)
				"status" => "orders_status",
				"comment_client" => "comments",
				"created" => "date_purchased",
				
				"client~client_id" => "customers_id",
				"client~gender" => "customers_gender",
				"client~first_name" => "customers_firstname",
				"client~last_name" => "customers_lastname",
				"client~company" => "customers_company",
				"client~street" => "customers_street_address",    //
				//"client~street_no" => "customers_street_address", // tuka najverojatno treba da parsiras po space od "customers_street_address" 
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
				//"delivery_address~street_no" => "delivery_street_address",  // tuka najverojatno treba da parsiras po space od "customers_street_address" 
				"delivery_address~zip_code" => "delivery_postcode",
				"delivery_address~city" => "delivery_city",
				"delivery_address~country" => "delivery_country",
				
				"items~item~item_id" => "orders_products_id",
				"items~item~product_id" => "products_id",
				"items~item~product_art_no" => "products_model",  //(we need ETRON number)
				"items~item~name" => "products_name",      // 
				//"items~item~name_add" => "products_name",  // tuka najverojatno treba da parsiras po space vo "products_name" 
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
			
			$this->predefined_attributes = array(
				'currency' => 'EUR'
			);
			
			$this->xml_mappings = array(
				'gender' => array('Frau' => 'f', 
								  'Herr' => 'm'),
				
			);
			
			$this->allowed_arrays = array(
				'client',
				'delivery_address', 
				'items', 
				'item'
			);
			
			$this->node_joins = array(
				'customers_street_address' => 'street_no',
				'delivery_street_address' => 'street_no', 
				'products_name' => 'name_add'
			);
			
			$this->add_same_nodes = array(
				'payment_class' => 'payment_method;paymentType'
			);
			
			$this->empty_nodes = array(
				'cc_type', 				//="Mastercard" - dont know, leave empty
				'shipping_class',		// - not known, leave empty
				'shipping_method', 		//- not known, leave empty
				'financialInstitution'	//="MC" - not known, leave empty
			);
			
			$this->tax_mappings = array(
				10 => 2,
				12 => 1
			);
			
			$this->orders_response_methods = array(
				1 => 'setOrderShipped',
				2 => 'setOrderCancelled',
				3 => 'setOrderReturned'
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
					if ($response['orders']['paging']['total'] == 1){
						$tmp_order = $response['orders']['order'];
						
						if (isset($tmp_order['items'])){
							$tmp_items = $tmp_order['items']['item'];						
							unset($tmp_order['items']['item']);
													
							if (!isset($tmp_items[0]))
								$tmp_order['items'][] = $tmp_items;
							else 
								$tmp_order['items'] = $tmp_items;
							
							unset($tmp_items);
						}
						
						$orders_array[] = $tmp_order;
						unset($tmp_order);
					}
					else {
						foreach ($response['orders']['order'] as $order){
							$tmp_order = $order;
						
							if (isset($tmp_order['items'])){
								$tmp_items = $tmp_order['items']['item'];						
								unset($tmp_order['items']['item']);
														
								if (!isset($tmp_items[0]))
									$tmp_order['items'][] = $tmp_items;
								else 
									$tmp_order['items'] = $tmp_items;
								
								unset($tmp_items);
							}
							
							$orders_array[] = $tmp_order;
							unset($tmp_order);
						}
						if ($response['orders']['paging']['pages'] > 1){
							$page = 2;
							
							while ($page <= $response['orders']['paging']['pages']){
								$response_other = parent::doRequest($url, $post, array(
									'key' => $this->key, 
									'page' => $page, 
									'per_page' => $this->orders_page_limit
								));
								
								$page++;
								
								foreach ($response_other['orders']['order'] as $order){
									$tmp_order = $order;
						
									if (isset($tmp_order['items'])){
										$tmp_items = $tmp_order['items']['item'];						
										unset($tmp_order['items']['item']);
																
										if (!isset($tmp_items[0]))
											$tmp_order['items'][] = $tmp_items;
										else 
											$tmp_order['items'] = $tmp_items;
										
										unset($tmp_items);
									}
									
									$orders_array[] = $tmp_order;
									unset($tmp_order);
								}
							}
						}	
					}					
				}
			}
			
			$this->existing_orders = $this->prepareOrders($orders_array);
			
			return $this->existing_orders;
		}

		function prepareOrders($orders){
			$params_array = array_flip($this->from_xml_attributes);
			$new_orders_array = array();
			
			foreach ($orders as $order){
				$new_order = array();
				
				foreach ($params_array as $key => $value){
					if (!strstr($value, '~')){
						if (!isset($order[$value])){
							if (isset($this->predefined_attributes[$key]))
								$new_order[$key] = $this->predefined_attributes[$key];
							else 
								$new_order[$key] = '';
						}
						else {
							if (is_array($order[$value]) && !in_array($value, $this->allowed_arrays))
								$new_order[$key] = '';
							else 
								$new_order[$key] = $order[$value];
							
							if (array_key_exists($key, $this->add_same_nodes)){
								$extra_nodes = explode(';', $this->add_same_nodes[$key]);
								if (sizeof($extra_nodes)){
									foreach ($extra_nodes as $one_more_node)
										$new_order[$one_more_node] = $order[$value];
								}
							}
						}							
					}
					else {
						$depth = explode('~', $value);
						if (sizeof($depth) == 2){
							$order_node_value = $order[$depth[0]][$depth[1]];
							
							if (array_key_exists($depth[1], $this->xml_mappings))
								$new_order[$key] = $this->xml_mappings[$depth[1]][$order_node_value];
							else {
								if (is_array($order_node_value) && !in_array($depth[1], $this->allowed_arrays))
									$new_order[$key] = '';
								else 
									$new_order[$key] = $order_node_value;
							}
							
							// just in case fields need to be concatenated with another node
							// example: <street> & <street_no>
							if (array_key_exists($key, $this->node_joins)){
								$new_order[$key] .= " " . $order[$depth[0]][$this->node_joins[$key]];
							}
						}
						else if (sizeof($depth) == 3){ // has to be the ordered items
							$order_node_array = $order[$depth[0]];
							
							$count = 0;
							foreach ($order_node_array as $order_item){
								if (is_array($order_item[$depth[2]]) && !in_array($depth[2], $this->allowed_arrays))
									$new_order['orders_products'][$count][$key] = '';
								else 
									$new_order['orders_products'][$count][$key] = $order_item[$depth[2]];
								
								// just in case fields need to be concatenated with another node
								// example: <product> & <name_add>
								if (array_key_exists($key, $this->node_joins)){
									$joined_value = $order_item[$this->node_joins[$key]];
									if (is_array($joined_value) && !in_array($this->node_joins[$key], $this->allowed_arrays))
										$joined_value = '';
									
									$new_order['orders_products'][$count][$key] .= " " . $joined_value;
								}
								$count++;	
							}				
						}
					}
				}

				if (is_array($this->empty_nodes) && sizeof($this->empty_nodes)){
					foreach ($this->empty_nodes as $empty_node)
						$new_order[$empty_node] = "";
				}

				$new_orders_array[] = $new_order;
			}
			
			return $new_orders_array;
		}



		/* DEAL WITH ORDER STATUS RESPONSES! */
		function updateOrder($order){
			if ((int)$order['status'] >= 1 && (int)$order['status'] <= 3){
				if (strlen($order['order_no']) != 9){
					Utils::log("Invalid order ID, not with 9 digits");
					return false;
				}
				
				$order_no_chunks = array();
				$order_no_chunks[0] = substr($order['order_no'], 0, 3);
				$order_no_chunks[1] = substr($order['order_no'], 3, 3);
				$order_no_chunks[2] = substr($order['order_no'], 6, 3);
				
				$order['order_no'] = implode('-', $order_no_chunks); 
				
				$method = $this->orders_response_methods[$order['status']];
				Utils::log("calling $method");
				return $this->$method($order['order_no']);
			}				
			else 
				Utils::log("Status " . $order['status'] . ' is not supported here');
		}
		
		function setOrderShipped($order_no){
			$method = 'setOrderShipped';
				
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			$request_array = array(
				'key' => $this->key,
				'order_no' => $order_no
			);
			
			Utils::log($url . '?' . http_build_query($request_array));
			
			$result = parent::doRequest($url, $post, $request_array);
			
			if ($result['success'] == 1){
				Utils::log("Order $order_no is set to 'shipped'");
				return true;
			}
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}
		
		function setOrderCancelled($order_no){
			$method = 'setOrderCancelled';
				
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			$request_array = array(
				'key' => $this->key,
				'order_no' => $order_no
			);
			
			Utils::log($url . '?' . http_build_query($request_array));
			
			$result = parent::doRequest($url, $post, $request_array);
			
			if ($result['success'] == 1){
				Utils::log("Order $order_no is set to 'cancelled'");
				return true;
			}
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}
		
		function setOrderReturned($order_no){
			$method = 'setOrderReturned';
				
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			$request_array = array(
				'key' => $this->key,
				'order_no' => $order_no, 
				'type' => 'fully'
			);
			
			Utils::log($url . '?' . http_build_query($request_array));
			
			$result = parent::doRequest($url, $post, $request_array);
			
			if ($result['success'] == 1){
				Utils::log("Order $order_no is set to 'returned'");
				return true;
			}
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}
		/*****************************************************************/
		
		
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