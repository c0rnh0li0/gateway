<?php
	namespace Rakuten\Handlers;
	
	use Rakuten, 
		Gateway\Utils;
	
	require_once(WWW_DIR . "/../libs/Rakuten/Rakuten.php");
	
	class Products extends \Rakuten\Rakuten {
		var $product;
		var $product_images;
		var $product_variants;
		var $product_errors;
		var $product_from_xml;
		var $from_attributes;
		var $product_response;
		
		var $delivery_rules;
		
		var $array_of_properties;
		var $product_compare;
		var $available_variant_labels;
		var $tax_percentage;
		
		var $group = "products";
		
		var $products_page_limit = 100;
		
		var $existing_products;
		
		function __construct($key, $domain){
			parent::__construct($key, $domain);
		
			$this->product = array(
				'product_id' => '', 			// int: ID of the product
				'product_art_no' => '', 		// string: Of the product item number
				'name' => '', 					// string: Name of product
				'price' => '', 					// Float: Price is not present in a product with variations!
				'price_reduced' => '', 			// Float: Reduced price is not present in a product with variations!
				'price_reduced_type' => '', 	// Float: Terms of reduced price 
												// RRP = Original price is retail price; 
												// VK = Original price is old selling price; 
												// NOTE: Is not present in a product with variations!
				'shipping_group' => '1', 		// int: Shipping group
				'available' => '', 				// bool: Availability
				'producer' => '', 				// string: Manufacturer
				'stock_policy' => '', 			// bool: Inventory management
				'stock' => '', 					// int: Inventory is not present in a product with variations!
				'delivery' => '', 				// int: Delivery time 
												// 0 = Immediate dispatch (delivery time 1-4 business days)
												// 3 = Ready for dispatch within 3 working days (delivery time 4-6 business days)
												// 5 = dispatched within 5 business days (delivery time 6-8 business days)
												// 7 = ships in 7 business days (delivery time 8-10 business days)
												// 10 = ships in 10 working days (Delivery time 10-15 business days)
												// 15 = ships in 15 business days (delivery time 15-20 business days)
												// 20 = ships in 20 business days (delivery time 20-30 business days)
												// 30 = ships in 30 business days (delivery time 30-40 business days)
												// 40 = ships in 40 business days (delivery time 40-50 business days)
												// 50 = ships in 50 business days (delivery time 50-60 business days)
												// 60 = ships in 60 working days (Delivery times longer than 3 months)
												// NOTE: Is not present in a product with variations!
				'min_order_qty' => '', 			// int: Minimum Quantity
				'staggering' => '', 			// String: Graduation - The format of the staggering is 2,4,6,8.
				'baseprice_unit' => '', 		// String: Unit of the reference amount 
												//			  ml = Milliliter; 
												//			  l = Liter; 
												//			  g = Gram; 
												//		      kg = Kilogram; 
												//			  cm = Centimeter; 
												//			  m = Meter; 
												//			  m² = Square meter; 
												//			  m³ = Cubic meters; 
												// NOTE: Is not present in a product with variations!
				'baseprice_volume' => '', 		// Float: Reference quantity is not present in a product with variations!
				'tax' => '1', 					// int: VAT - 1 = 19%;
												//			  2 = 7%;
												// 			  3 = 0%;
												// 			  4 = 10.7%; 
												// 			  10 = 10%; 
												//			  11 = 12%; 
												//			  12 = 20%
				'homepage' => '', 				// bool: 
												// 0 = Will not be displayed on the home page in the shop
												// 1 = Will be displayed on the home page in the shop
				'connect' => '', 				// bool: Disabled for paying Sign in! Modules
				'ean' => '', 					// string: EAN is not present in a product with variations!
				'isbn' => '', 					// string: ISBN is not present in a product with variations!
				'mpn' => '', 					// string: MPN (unique manufacturer's part number) is not present in a product with variations!
				'inci' => '', 					// string: Synopses
				'description' => '', 			// string: Product Description
				'comment' => '', 				// string: Comment
				'cross_selling_title' => '', 	// string: Title of cross-selling
				'shop_category_id' => '',		// int: ID of a shop category
				'rakuten_category_id' => '',	// int: Rakuten ID of a portal category
			);	
			
			$this->product_images = array(	// Container with pictures Does not include data if no images were found!
				'image' => array(			// container with data to an image
					'image_id' => '',		// int: ID
					'src' => '',			// URL: URL
					'default' => '',		// bool: Indicator for the product main image
					'comment' => '',		// string: Comment
				)
			);
			
			$this->product_variants = array(	// Container with variants Does not include data if no variants were found!
				'label' => '',					// string: About designation of variations (eg colors)
				'variant' => array(				// Container with data to a variant
					'variant_id' => '',			// Integer	ID of the variant
					'variant_art_no' => '',		// String	Item number
					'name' => '',				// String	Designation (eg blue)
					'price' => '',				// Float	Price
					'price_reduced' => '',		// Float	Reduced price
					'price_reduced_type' => '',	// String	Terms of the reduced price
												// RRP	=	Original price is retail price
												// VK	=	Original price is old selling price
					'baseprice_unit' => '',		// String	Unit of the reference amount
												// ml	=	Milliliter
												// l	=	Liter
												// g	=	Gram
												// kg	=	Kilogram
												// cm	=	Centimeter
												// m	=	Meter
												// m²	=	Square meter
												// m³	=	Cubic meters
					'baseprice_volume' => '',	// Float	Reference amount
					'stock' => '',				// Integer	Stock
					'delivery' => '',			// Integer	Delivery time
												// 0	=	Immediate dispatch (delivery time 1-4 business days)
												// 3	=	Ready for dispatch within 3 working days (delivery time 4-6 business days)
												// 5	=	dispatched within 5 business days (delivery time 6-8 business days)
												// 7	=	ships in 7 business days (delivery time 8-10 business days)
												// 10	=	ships in 10 working days (Delivery time 10-15 business days)
												// 15	=	ships in 15 business days (delivery time 15-20 business days)
												// 20	=	ships in 20 business days (delivery time 20-30 business days)
												// 30	=	ships in 30 business days (delivery time 30-40 business days)
												// 40	=	ships in 40 business days (delivery time 40-50 business days)
												// 50	=	ships in 50 business days (delivery time 50-60 business days)
												// 60	=	ships in 60 working days (Delivery times longer than 3 months)
					'available' => '',			// Bool	Availability
					'isbn' => '',				// String	ISBN
					'ean' => '',				// String	EAN
					'mpn' => '',				// String	MPN (Manufacturer's unique item number)
				)
			);
			
			$this->product_variants_from_product = array(
				'label' => '',
				'variant' => array(
					'variant_id' => 'product_id',
					'variant_art_no' => 'product_art_no',
					'name' => 'name',
					'price' => 'price',
					'price_reduced' => 'price_reduced',
					'price_reduced_type' => 'price_reduced_type',
					'baseprice_unit' => 'baseprice_unit',
					'baseprice_volume' => 'baseprice_volume',
					'stock' => 'stock',	
					'delivery' => 'delivery',
					'available' => 'available',
					'isbn' => 'isbn',
					'ean' => 'ean',	
					'mpn' => 'mpn',
				)
			);
			
			$this->errors = array(
				1105 => "The product ID or item number was not transferred",
				1106 => "The URL or image parameter was not passed",
				1110 => "The product could not be found",
				1115 => "The replaced image could not be found",
				1120 => "The maximum number of 5 images is achieved",
				1123 => "The image could not be downloaded from the URL",
				1124 => "The image has an invalid format. Permits are JPG, GIF and PNG",
				1125 => "The image could not be saved",
				1130 => "The image is already assigned to the product",
				1005 => 'The article number is in the product inventory already exists',
				1010 => 'The ISBN number is invalid',
				1011 => 'The EAN number is invalid',
				1015 => 'The shipping group could not be found',
				1020 => 'The reduced price is not more favorable than the standard price',
				1030 => 'The Rakuten category could not be found',
				1031 => 'The Shop Category was not found', 
				2205 => 'The product ID or item number was not transferred',
				2210 => 'The product could not be found',
				2212 => 'The parameter is not valid when a product with variants',
				2215 => 'The article number is in the product inventory already exists',
				2220 => 'The reduced price is not more favorable than the standard price',
				2222 => 'The product may not be visible due to missing images',
				2231 => 'The Rakuten category could not be found',
				2232 => 'The Rakuten category was assigned by Rakuten and therefore can not be changed',
				2240 => 'The ISBN number is invalid',
				2241 => 'The EAN number is invalid',
				2245 => 'The shipping group could not be found', 
				2505 => 'The product ID or item number was not transferred',
				2510 => 'The product could not be found', 
				1205 => 'The product ID or item number was not transferred',
				1210 => 'The product could not be found',
				1212 => 'The variant name is not passed',
				1215 => 'The variant part number is in the product inventory already exists',
				1220 => 'The reduced price is not more favorable than the standard price',
				1225 => 'The ISBN number is invalid',
				1230 => 'The EAN number is invalid', 
				1505 => 'The product ID or item number was not transferred',
				1510 => 'The product could not be found'
			);
			
			
			
			
			$this->product_response = array(
				'success' => 1,
				'product_id' => ''
			);
			
			// use array_flip
			$this->product_from_xml = array(
				'product_id' => 'products_id', 						// int: ID of the product
				'product_art_no' => 'products_model',				// FROM ATTRIBUTES // string: Item number
				'name' => 'products_description~name', 				// PRODUCTS_DESCRIPTION - PRODUCTS_NAME // string: Name of product
				//'price' => 'products_price|products_regularprice', 	// Float: Price is not present in a product with variations!
				//'price_reduced' => 'products_priceb', 				// ????????			// Float: Reduced price is not present in a product with variations!
				'price' => 'products_regularprice', 				// Float: Price is not present in a product with variations!
				'price_reduced' => 'products_price', 				// ????????			// Float: Reduced price is not present in a product with variations!
				
				'price_reduced_type' => '', 						// Float: Terms of reduced price 
																	// RRP = Original price is retail price; 
																	// VK = Original price is old selling price; 
																	// NOTE: Is not present in a product with variations!
				'shipping_group' => '', 							// int: Shipping group
				'available' => 'products_status', 					// bool: Availability
				'producer' => 'manufacturers_id', 					// string: Manufacturer
				'stock_policy' => '',								// bool: Inventory management
				'stock' => 'products_quantity', 					// int: Inventory is not present in a product with variations!
				'delivery' => 'products_shippingtime', 				// int: Delivery time 
																	// 0 = Immediate dispatch (delivery time 1-4 business days)
																	// 3 = Ready for dispatch within 3 working days (delivery time 4-6 business days)
																	// 5 = dispatched within 5 business days (delivery time 6-8 business days)
																	// 7 = ships in 7 business days (delivery time 8-10 business days)
																	// 10 = ships in 10 working days (Delivery time 10-15 business days)
																	// 15 = ships in 15 business days (delivery time 15-20 business days)
																	// 20 = ships in 20 business days (delivery time 20-30 business days)
																	// 30 = ships in 30 business days (delivery time 30-40 business days)
																	// 40 = ships in 40 business days (delivery time 40-50 business days)
																	// 50 = ships in 50 business days (delivery time 50-60 business days)
																	// 60 = ships in 60 working days (Delivery times longer than 3 months)
																	// NOTE: Is not present in a product with variations!
				'min_order_qty' => '', 								// int: Minimum Quantity
				'staggering' => '', 								// String: Graduation - The format of the staggering is 2,4,6,8.
				'baseprice_unit' => '', 							// String: Unit of the reference amount 
																	//			  ml = Milliliter; 
																	//			  l = Liter; 
																	//			  g = Gram; 
																	//		      kg = Kilogram; 
																	//			  cm = Centimeter; 
																	//			  m = Meter; 
																	//			  m² = Square meter; 
																	//			  m³ = Cubic meters; 
																	// NOTE: Is not present in a product with variations!
				'baseprice_volume' => 'attributes~inh_einheit', 	// Float: Reference quantity is not present in a product with variations!
				'tax' => 'products_tax_class_id', 					// int: VAT - 1 = 19%;
																	//			  2 = 7%;
																	// 			  3 = 0%;
																	// 			  4 = 10.7%; 
																	// 			  10 = 10%; 
																	//			  11 = 12%; 
																	//			  12 = 20%
				'homepage' => 'products_startpage', 				// bool: 
																	// 0 = Will not be displayed on the home page in the shop
																	// 1 = Will be displayed on the home page in the shop
				'connect' => '', 									// bool: Disabled for paying Sign in! Modules
				'ean' => 'products_model', 							// string: EAN is not present in a product with variations!
				'isbn' => '', 										// string: ISBN is not present in a product with variations!
				'mpn' => '', 										// string: MPN (unique manufacturer's part number) is not present in a product with variations!
				'inci' => 'attributes~besonderheit', 				// string: Synopses
				'description' => 'products_description~description',  // string: Product Description
				'comment' => 'attributes~allgemeines', 				// string: Comment
				'cross_selling_title' => 'products_description~name', 	// ?????????? // string: Title of cross-selling
				
				// cycle through shop categories for these
				'shop_category_id' => '',							// int: ID of a shop category
				'rakuten_category_id' => '',						// int: Rakute ID of a portal category
			);
			
			$this->from_attributes = array(
				'allgemeines', 
				'besonderheit',
				'inh_einheit'
			);
			
			
			$this->array_of_properties = array(
				'categories', 
				'images',
				'attributes',
				'variants', 
				'cross_sellings'
			);
			
			$this->product_compare = array(
				'name',
				'price',
				'price_reduced',
				'price_reduced_type', 
				'shipping_group',
				'available',
				'producer',
				'stock_policy',
				'stock',
				'delivery', 
				'min_order_qty',
				'staggering',
				'baseprice_unit', 
				'baseprice_volume',
				'tax',
				'homepage', 
				'connect',
				'ean',
				'isbn',
				'mpn',
				'inci',
				'description',
				'comment',
				'cross_selling_title',
				'shop_category_id',
				'rakuten_category_id',
				'categories' => array(
					
				),
				'images' => array( 
					//'image_id',
					'src',
					//'default',
					//'comment'
				), 
				'attributes' => array(
					'title', 
					'value'
				),
				'variants' => array(
					//'variant_id' => 'product_id',
					//'variant_art_no' => 'product_art_no',
					//'label' => '',
					'name' => 'name',
					'price' => 'price',
					'price_reduced' => 'price_reduced',
					'price_reduced_type' => 'price_reduced_type',
					'baseprice_unit' => 'baseprice_unit',
					'baseprice_volume' => 'baseprice_volume',
					'stock' => 'stock',	
					'delivery' => 'delivery',
					'available' => 'available',
					'isbn' => 'isbn',
					'ean' => 'ean',	
					'mpn' => 'mpn',
				)
			);
			
			
			// 
			$this->available_variant_labels = array(
				'products_model_info' => 'size', 
				'color' => 'color'
			);			 
			
			$this->delivery_rules = array(
				0,		//=	Immediate dispatch (delivery time 1-4 business days)
				3,		//=	Ready for dispatch within 3 working days (delivery time 4-6 business days)
				5,		//=	dispatched within 5 business days (delivery time 6-8 business days)
				7,		//=	ships in 7 business days (delivery time 8-10 business days)
				10,		//=	ships in 10 working days (Delivery time 10-15 business days)
				15,		//=	ships in 15 business days (delivery time 15-20 business days)
				20,		//=	ships in 20 business days (delivery time 20-30 business days)
				30,		//=	ships in 30 business days (delivery time 30-40 business days)
				40,		//=	ships in 40 business days (delivery time 40-50 business days)
				50,		//=	ships in 50 business days (delivery time 50-60 business days)
				60,		//=	ships in 60 working days (Delivery times longer than 3 months)
			);
			
			$this->delivery_mappings = array(
				1 	=> 0,
				2 	=> 3,
				3 	=> 3,
				4 	=> 5,
				5 	=> 5,
				6 	=> 7,
				7 	=> 10,
				8 	=> 15,
				9 	=> 20,
				10 	=> 30,
				11 	=> 40,
				12 	=> 50,
				13 	=> 60, 
				14 	=> 60,
				15 	=> null,
				16 	=> null
			);
			
			$this->tax_mappings = array(
				2 => 10,
				1 => 12
			);
			
			$this->tax_percentage = array(
				10 => 10,
				12 => 20
			);
		}	

		function buildProduct($product_xml, $attributes_array, $is_variant = false, $tax = null){
			$product_data = array();
			$xml_attrs = $this->product_from_xml;
			
			foreach ($xml_attrs as $key => $value){
				//echo "$key => $value<br />";
				
				//$current_val = $value;
				//$key = $value;
				
				if (!empty($value)){
					if (strstr($value, '~')){
						$tmp = explode('~', $value);
						
						if ($tmp[0] == 'attributes')
							$product_data[$key] = $this->getAttributeValue($tmp[1], $attributes_array['attributes']);
						else 
							$product_data[$key] = (string) $attributes_array[$tmp[0]][$tmp[1]];
					}
					else if (strstr($value, '|')){
						$tmp = explode('|', $value);
						$tmp_value = '';
						if (!empty($product_xml[$tmp[0]]))
							$tmp_value = (string) $product_xml[$tmp[0]];
						else 
							$tmp_value = (string) $product_xml[$tmp[1]];
						
						$product_data[$key] = $tmp_value;
					}
					else 
						$product_data[$key] = (string) $product_xml[$value];
					
				}
				else
					$product_data[$key] = '';
			}
			
			if ($is_variant){
				if (isset($tax) && isset($this->tax_percentage[$tax]))
					$product_data['price'] = (float)$product_data['price'] + (((float)$product_data['price'] * (int)$this->tax_percentage[$tax]) / 100); 
				$product_data['price'] = number_format((float)$product_data['price'], 2, '.', '');
				
				$temp_variant = array();
				foreach ($this->product_variants_from_product['variant'] as $key => $value)
					$temp_variant[$key] = $product_data[$value];
				
				$product_data = $temp_variant;
				unset($temp_variant);
				
				//$product_data['attributes'] = $this->getProductAttributes($product_data['product_art_no']);
				
				unset($product_data['product_id']);
				unset($product_data['product_art_no']);			
				unset($product_data['categories']);
				unset($product_data['images']);
				
			}
			else {
				$product_data['categories'] = $attributes_array['categories'];
				$product_data['images'] = $attributes_array['images'];	
			}
			
			
			//$product_data['price'] = number_format((float)$product_data['price'], 2, '.', '');
			if (isset($product_data['price_reduced']) && $product_data['price_reduced'] != '')
				$product_data['price_reduced'] = number_format((float)$product_data['price_reduced'], 2, '.', '');
			
			// deal with taxes from mappings
			if (isset($product_data['tax']) && isset($this->tax_mappings[$product_data['tax']]))
				$product_data['tax'] = $this->tax_mappings[$product_data['tax']];
			
			// deal with delivery from mappings	
			if (isset($product_data['delivery']) && isset($this->delivery_mappings[$product_data['delivery']]))
				$product_data['delivery'] = $this->delivery_mappings[$product_data['delivery']];
			if (!$product_data['delivery'] || $product_data['delivery'] === null)
			 	unset($product_data['delivery']);
				
			if (isset($product_data['tax']) && isset($this->tax_percentage[$product_data['tax']]))
				$product_data['price'] = (float)$product_data['price'] + (((float)$product_data['price'] * (int)$this->tax_percentage[$product_data['tax']]) / 100); 
			$product_data['price'] = number_format((float)$product_data['price'], 2, '.', '');
			
			return $product_data;
		}

		function getAttributeValue($key, $attributes) {
			foreach ($attributes as $attribute)
				if ($attribute['name'] == $key)
					return $attribute['value'];
		}
		
		
		function prepareProduct($product){
			if (isset($product['price_reduced']) && $product['price_reduced'] == '')
				unset($product['price_reduced']);
			
			if (isset($product['baseprice_unit']) && $product['baseprice_unit'] == '')
				unset($product['baseprice_unit']);
			
			if (isset($product['staggering']) && $product['staggering'] == '')
				unset($product['staggering']);
			
			if (isset($product['stock_policy']) && $product['stock_policy'] == '')
				unset($product['stock_policy']);

			if (isset($product['min_order_qty']) && $product['min_order_qty'] == '')
				unset($product['min_order_qty']);						
						
			if (isset($product['price_reduced_type']) && $product['price_reduced_type'] == '')
				unset($product['price_reduced_type']);
			
			if (isset($product['shipping_group']) && $product['shipping_group'] == '')
				unset($product['shipping_group']);
			
			if (isset($product['baseprice_volume']) && $product['baseprice_volume'] == '')			
				unset($product['baseprice_volume']);
			
			if (isset($product['connect']) && $product['connect'] == '')			
				unset($product['connect']);
			
			if (isset($product['delivery']) && $product['delivery'] == '')			
				unset($product['delivery']);
			
			if (isset($product['shop_category_id']) && $product['shop_category_id'] == '')			
				unset($product['shop_category_id']);
				
			if (isset($product['rakuten_category_id']) && $product['rakuten_category_id'] == '')			
				unset($product['rakuten_category_id']);
			
			if (isset($product['tradoria_category_id']) && $product['tradoria_category_id'] == '')
				unset($product['tradoria_category_id']);
			
			
			
			if (isset($product['delivery']) && $product['delivery'] == '')			
				unset($product['delivery']);
			
			//$product['delivery'] = $this->closestDelivery($product['delivery']);
			
			if (isset($product['description']) && $product['description'] == '')
				$product['description'] = '<p>&nbsp;</p>';			
				//unset($product['description']);
			
			if (isset($product['tax']) && ($product['tax'] == '' || $product['tax'] == 0))			
				unset($product['tax']);
			//$product['tradoria_category_id'] = 0;
			//$product['shop_category_id'] = 0;
			//$product['rakuten_category_id'] = 0;
			
			//$product['title'] = $product['name'];
			
			return $product;
		}

		function closestDelivery($delivery) {
			sort($this->delivery_rules);
		    foreach ($this->delivery_rules as $a) {
		        if ($a >= $delivery) return $a;
		    }
		    return first($this->delivery_rules); // or return NULL;
		}

		function prepareProductForUpdate($product){
			$product = $this->prepareProduct($product);
			
			if (isset($product['variants']) && is_array($product['variants']) && sizeof($product['variants'])){
				unset($product['price']);
				unset($product['product_id']);
				unset($product['stock']);
				unset($product['delivery']);
				unset($product['available']);
				unset($product['isbn']);
				unset($product['ean']);
				unset($product['mpn']);	
			}
						
			return $product;
		}
		
		function getProducts($product_model_id = null){
			$method = 'getProducts';
			$products_array = array();
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$page = 1;
			$post = 0;
			
			$params_array = array(
				'key' => $this->key, 
				'page' => $page, 
				'per_page' => $this->products_page_limit
			);
			
			//echo $url . '?' . http_build_query($params_array);
			
			$response = parent::doRequest($url, $post, $params_array);
			
			if ($response['success'] == 1){
				if (isset($response['products']['product']) && is_array($response['products']['product']) && sizeof($response['products']['product'])){
					if ($response['products']['paging']['total'] == 1){
						$tmp_product = $response['products']['product'];
						
						if (isset($tmp_product['variants'])){
							$tmp_variants = $tmp_product['variants']['variant'];						
							unset($tmp_product['variants']['variant']);
													
							if (!isset($tmp_variants[0]))
								$tmp_product['variants'][] = $tmp_variants;
							else 
								$tmp_product['variants'] = $tmp_variants;
							
							unset($tmp_variants);
						}
						
						$product_attributes = $this->getProductAttributes($tmp_product['product_art_no']); 
						$product_categories = $this->getProductCategories($tmp_product['product_art_no']); 
						
						if ($product_attributes)
							$tmp_product['attributes'] = $product_attributes;
						
						if ($product_categories)
							$tmp_product['categories'] = $product_categories;
						
						$product_cross_selling = $this->getProductCrossSellings($tmp_product['product_art_no']);				
						if ($product_cross_selling)
							$tmp_product['cross_sellings'] = $product_cross_selling;
						
										
						$products_array[] = $tmp_product;
						
						unset($tmp_product);
					}
					else {
						foreach ($response['products']['product'] as $product){
							$tmp_product = $product;
							
							if (isset($tmp_product['variants'])){
								$tmp_variants = $tmp_product['variants']['variant'];						
								unset($tmp_product['variants']['variant']);
														
								if (!isset($tmp_variants[0]))
									$tmp_product['variants'][] = $tmp_variants;
								else 
									$tmp_product['variants'] = $tmp_variants;
								
								unset($tmp_variants);
							}
							
							$product_attributes = $this->getProductAttributes($tmp_product['product_art_no']); 
							$product_categories = $this->getProductCategories($tmp_product['product_art_no']); 
							
							if ($product_attributes)
								$tmp_product['attributes'] = $product_attributes;
							
							if ($product_categories)
								$tmp_product['categories'] = $product_categories;
										
							$product_cross_selling = $this->getProductCrossSellings($tmp_product['product_art_no']);
							if ($product_cross_selling)
								$tmp_product['cross_sellings'] = $product_cross_selling;
						
							$products_array[] = $tmp_product;
							
							unset($tmp_product);
						}
							
						
						if ($response['products']['paging']['pages'] > 1){
							$page = 2;
							
							while ($page <= $response['products']['paging']['pages']){
								$response_other = parent::doRequest($url, $post, array(
									'key' => $this->key, 
									'page' => $page, 
									'per_page' => $this->products_page_limit
								));
								
								$page++;
								
								foreach ($response_other['products']['product'] as $product){
									$tmp_product = $product;
									
									if (isset($tmp_product['variants'])){
										$tmp_variants = $tmp_product['variants']['variant'];						
										unset($tmp_product['variants']['variant']);
																
										if (!isset($tmp_variants[0]))
											$tmp_product['variants'][] = $tmp_variants;
										else 
											$tmp_product['variants'] = $tmp_variants;
										
										unset($tmp_variants);
									}
							
									$product_attributes = $this->getProductAttributes($tmp_product['product_art_no']); 
									$product_categories = $this->getProductCategories($tmp_product['product_art_no']); 
									
									if ($product_attributes)
										$tmp_product['attributes'] = $product_attributes;
									
									if ($product_categories)
										$tmp_product['categories'] = $product_categories;
									
									$product_cross_selling = $this->getProductCrossSellings($tmp_product['product_art_no']);				
									if ($product_cross_selling)
										$tmp_product['cross_sellings'] = $product_cross_selling;
									
									$products_array[] = $tmp_product;
									
									unset($tmp_product);
								}
							}
						}	
					}					
				}
				
			}
			
			if (sizeof($products_array)){
				for ($i = 0; $i < count($products_array); $i++){
					if (isset($products_array[$i]) && is_array($products_array[$i])){
						foreach ($products_array[$i] as $key => $value){
							if (is_array($products_array[$i][$key]) && !in_array($key, $this->array_of_properties))
								$products_array[$i][$key] = '';
						}
					} 
					else {
						foreach ($products_array as $key => $value){
							if (is_array($products_array[$key]) && !in_array($key, $this->array_of_properties))
								$products_array[$key] = '';
						}
					}
				}
			}
			
			// foreach ($products_array as $pr){
				// $this->deleteProduct($pr);
			// }
			
			$this->existing_products = $products_array;
			return $products_array;
		}

		function getProductByID($product_id){
			foreach ($this->existing_products as $product){
				if ($product['product_id'] == $product_id)
					return $product;
			}
			
			return false;
		}
		
		
		/* Product main API methods */
		function addProduct($product_data){
			//echo "ADDING PRODUCT: ". $product_data['product_art_no'] . "<br />";
			
			$method = 'addProduct';
			$post = 1;
			
			$product = $this->prepareProduct($product_data);
			
			$request_params = $product;
			$request_params['key'] = $this->key; 
			unset($request_params['images']);
			unset($request_params['categories']);
			unset($request_params['attributes']);
			unset($request_params['variants']);
			unset($request_params['product_id']);
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1){
				$has_error = false;
				
				// DONE
				if (isset($product['images']) && is_array($product['images']) && sizeof($product['images'])){
					//echo "adding images<br /><br />";
					
					foreach ($product['images'] as $image){						
						if (!$this->addProductImage($product['product_art_no'], $image))
							$has_error = true;
					}
				}
				
				if (isset($product['attributes']) && is_array($product['attributes']) && sizeof($product['attributes'])){
					//echo "adding attributes<br /><br />";
					foreach ($product['attributes'] as $attribute){
						if (!$this->addProductAttribute($product['product_art_no'], $attribute))
							$has_error = true;
					}
				}
				
				if (isset($product['categories']) && is_array($product['categories']) && sizeof($product['categories'])){
					//echo "adding categories<br /><br />";
					foreach ($product['categories'] as $category){
						if (!$this->addProductToShopCategory($product['product_art_no'], $category))
							$has_error = true;
					}
				}
				
				if (isset($product['variants']) && is_array($product['variants']) && sizeof($product['variants'])){
					//echo "adding variants<br /><br />";
					
					$variant_definitions = $this->getVariantDefinitions($product['variants']);
					$this->addProductVariantDefinition($variant_definitions, $product['product_art_no']);
					
					foreach ($product['variants'] as $variant){
						if (!$this->addProductVariant($product['product_art_no'], $variant, $variant_definitions))
							$has_error = true;
					}
				}
				
				if ($has_error){
					//echo "some error occured";
				}
				
				return true;
			}
				
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}

		function editProduct($product_data){
			//echo "EDITING PRODUCT: ". $product_data['product_art_no'] . "<br />";
			$method = 'editProduct';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$product = $this->prepareProductForUpdate($product_data);
			$product['key'] = $this->key;
			
			unset($product['attributes']);
			unset($product['categories']);
			unset($product['images']);
			unset($product['variants']);
			unset($product['existing_variants']);
			unset($product['existing_images']);
			unset($product['existing_categories']);
			
			//echo "EDIT URL: " . $url . '?' . http_build_query($product) . "<br /><br /><br />";
			
			$result = parent::doRequest($url, $post, $product);
			
			if ($result['success'] == 1){
				if (isset($product_data['images']) && is_array($product_data['images']) && sizeof($product_data['images']))
					$this->updateProductImages($product_data, $product['product_art_no']);
				
				if (isset($product_data['categories']) && is_array($product_data['categories']) && sizeof($product_data['categories']))
					$this->updateProductCategories($product_data, $product['product_art_no']);
				
				if (isset($product_data['attributes']) && is_array($product_data['attributes']) && sizeof($product_data['attributes']))
					$this->updateProductAttributes($product_data, $product['product_art_no']);
					
				if (isset($product_data['variants']) && is_array($product_data['variants']) && sizeof($product_data['variants']))
					$this->updateProductVariants($product_data, $product['product_art_no']);
				
				return true;
			}
				
			
			$this->error = $this->getErrorMessage($result);
			
			return false;			
		}

		function updateProductVariants($product_data, $product_art_no){
			$has_existing_variants = false;
			if (isset($product_data['existing_variants']) && is_array($product_data['existing_variants']) && sizeof($product_data['existing_variants']))
				$has_existing_variants = true;
				
			if ($has_existing_variants){
				if (isset($product_data['existing_variants']['label']))
					unset($product_data['existing_variants']['label']);
				
				foreach ($product_data['existing_variants'] as $old_variant){
					$variant_found = true;
					foreach ($product_data['variants'] as $variant){
						if ($old_variant['variant_art_no'] == $variant['variant_art_no']){
							$variant_found = true;
							break;
						}
					}
					
					if (!$variant_found)
						$this->deleteProductVariantByArtID($old_variant['variant_art_no']);
				}

				$has_existing_variants = true;
			}
			
			
			$variant_defs = $this->getVariantDefinitions($product_data['variants']);
			$variant_defs_str = implode(',', $variant_defs);
			
			foreach ($product_data['variants'] as $variant){
				$variant_found = false;
				
				if ($has_existing_variants){
					foreach ($product_data['existing_variants'] as $old_variant){
						if ($old_variant['variant_art_no'] == $variant['variant_art_no']){
							$variant_found = true;
							
							$variant_name_arr = array();
							
							$count = 1;
							if (isset($variant['attributes']) && is_array($variant['attributes']) && sizeof($variant['attributes'])){
								foreach ($variant['attributes'] as $attribute){
									if ($attribute['value'] !== '' && $attribute['name'] != ''){
										if (isset($this->available_variant_labels[$attribute['name']]) && $this->available_variant_labels[$attribute['name']] != '')
											$attribute['name'] = $this->available_variant_labels[$attribute['name']];
										
										if ($variant_defs['variant_' . $count] == $attribute['name']){
											$variant_name_arr[$count] = $attribute['value'];
											$count++;
										}
									}
								}
							}
	
							$variant['name'] = implode(',', $variant_name_arr);
							
							$is_changed = false;
							//$variant['delivery'] = $this->closestDelivery($variant['delivery']);
							foreach ($this->product_compare['variants'] as $key => $value){
								if (isset($variant[$key]) && $variant[$key] != '' && $old_variant[$key] != $variant[$key]){
									//echo "the variant difference: key = $key -> {$old_variant[$key]} != {$variant[$key]}<br />";
									$is_changed = true;
									//break;
								}
							}
							
							if ($is_changed){
								foreach ($this->product_compare['variants'] as $key => $value){
									if (isset($variant[$key]) && $variant[$key] == '')
										$variant[$key] = $old_variant[$key];
								}
								
								$this->editProductVariant($product_art_no, $variant, $variant_defs);
							}
							
							break;
						}
					}
				}				
				
				if (!$variant_found)
					$this->addProductVariant($product_art_no, $variant, $variant_defs);
			}
		}

		function updateProductAttributes($product_data, $product_art_no){
			$has_existing_attributes = false;
			if (isset($product_data['existing_variants']) && is_array($product_data['existing_variants']) && sizeof($product_data['existing_variants']))
				$has_existing_attributes = true;
				
			if ($has_existing_attributes){
				foreach ($product_data['existing_attributes'] as $old_attribute){
					$attribute_found = true;
					foreach ($product_data['attributes'] as $attribute){
						if ($old_attribute['title'] == $attribute['name']){
							$attribute_found = true;
							break;
						}
					}
					
					if (!$attribute_found)
						$this->deleteProductAttribute($product_art_no, $attribute);
				}	
			}			
			
			foreach ($product_data['attributes'] as $attribute){
				$attribute_found = false;
				
				if ($has_existing_attributes){					
					foreach ($product_data['existing_attributes'] as $old_attribute){
						if ($old_attribute['title'] == $attribute['name']){
							$attribute_found = true;
							
							if ($old_attribute['value'] != $attribute['value'])
								$this->editProductAttribute($product_art_no, $attribute);
						}
					}
				}

				if (!$attribute_found)
					$this->addProductAttribute($product_art_no, $attribute);
			}
		}


		function updateProductCategories($product_data, $product_art_no){
			$has_existing_categories = false;
			if (isset($product_data['existing_categories']) && is_array($product_data['existing_categories']) && sizeof($product_data['existing_categories']))
				$has_existing_categories = true;
			
			if ($has_existing_categories){
				foreach($product_data['existing_categories'] as $old_category){
					if (!in_array($old_category, $product_data['categories']))
						$this->deleteProductFromShopCategory($product_art_no, $old_category['external_category_id']);
				}
			}
			
			foreach($product_data['categories'] as $new_category){
				if	($has_existing_categories){
					if (!in_array($new_category, $product_data['existing_categories']))
						$this->addProductToShopCategory($product_art_no, $new_category);	
				}
				else 
					$this->addProductToShopCategory($product_art_no, $new_category);
			}			
		}
		
		function updateProductImages($product_data, $product_art_no){
			$has_existing_images = false;
			if (isset($product_data['existing_images']) && is_array($product_data['existing_images']) && sizeof($product_data['existing_images']))
				$has_existing_images = true;
			
			$product_existing_images = array();
			if (!isset($product_data['existing_images'][0][0])){
				$product_existing_images[] = $product_data['existing_images'][0];
			}
			else {
				foreach ($product_data['existing_images'][0] as $img)
					$product_existing_images[] = $img;
			}
			
			foreach ($product_data['images'] as $image){
				$image_prefix = substr($image, 0, 1);
				
				if ($image_prefix == '+')
					$this->addProductImage($product_art_no, substr($image, 1));
				else if ($image_prefix == '-'){
					$image_id = null;
					
					if ($has_existing_images){
						if (count($product_existing_images) > 0){
							$image_to_delete = substr($image, 1);
							foreach ($product_existing_images as $existing_image){
								if ($existing_image['comment'] == $image_to_delete)
									$image_id = $existing_image['image_id'];	
							}
						}
						
						$this->deleteProductImage($image_id);	
					}					
				}				
			}
		}
		
		function deleteProduct($product){
			/********* instead of deleting it, just make it unavailable ******/
			$method = 'deleteProduct';
			//$method = 'editProduct';
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_array = array(
				'key' => $this->key
			);
			
			if (!empty($product['product_art_no']))
				$request_array['product_art_no'] = $product['product_art_no'];
			else 
				$request_array['product_id'] = $product['product_id'];
			
			// instead of deleting it, just make it unavailable
			//$request_array['available'] = 0;
			
			$result = parent::doRequest($url, $post, $request_array);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false;
		}
		
		
		
		/* Product images API methods */
		function deleteProductImages($images){
			$has_errors = false;
			
			if (sizeof($images)){
				if (isset($images['image']['image_id']))
					return $this->deleteProductImage($images['image']['image_id']);
				else {
					foreach ($images['image'] as $image){
						if (!$this->deleteProductImage($image['image_id']))
							$has_errors = true;
					}
				}
			}
			
			die;
			
			return !$has_errors;
		}
		
		function addProductImage($product_art_no, $image){
			$method = 'addProductImage';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$image = preg_replace("/^[+|-]*/", "", $image);
			$image_name = $image;
			$image = $this->buildImageURL($image);
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no, 
				'url' => $image, 
				'comment' => $image_name		
			);
			
			//echo "image add url: " . print_r($request_params, true) . "<br />";
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		function deleteProductImage($image_id){
			$method = 'deleteProductImage';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_params = array(
				'key' => $this->key, 
				'image_id' => $image_id, 
				//'product_id ' => ''
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		
		
		/* Product variants API methods */
		function deleteProductVariants($variants){
			foreach ($variants as $variant){
				if (isset($variant['variant_id']) && !empty($variant['variant_id']))
					return $this->deleteProductVariantByID($variant['variant_id']);
				
				if (isset($variant['variant_art_no']) && !empty($variant['variant_art_no']))
					return $this->deleteProductVariantByArtID($variant['variant_art_no']);
			}
		}
		
		
		function getVariantDefinitions($variants){
			$count = 1;
			$variant_definitions = array();
			
			foreach ($variants as $variant){
				if (isset($variant['attributes']) && is_array($variant['attributes']) && sizeof($variant['attributes'])){
					foreach ($variant['attributes'] as $attribute){
						if ($count == 5)
							break;
						
						if ($attribute['value'] !== '' && $attribute['name'] != ''){
							if (strlen($attribute['name']) <= 20){
								if (!isset($variant_definitions['variant_' . $count]) || $variant_definitions['variant_' . $count] != $attribute['name'] && 
									!in_array($attribute['name'], $variant_definitions)){
									$variant_definitions['variant_' . $count] = $attribute['name'];
									$count++;
								}
							}
						}
					}
				}
			}
			
			$definitions = array_unique($variant_definitions);
			
			
			foreach ($definitions as $key => $value){
				if (isset($this->available_variant_labels[$value]) && $this->available_variant_labels[$value] != ''){
					$definitions[$key] = $this->available_variant_labels[$value];
				}			
			}
			
			return $definitions;			
		}
		
		function addProductVariant($product_art_no, $variant, $definitions = array()){
			//echo "ADDING PRODUCT VARIANT: ". $variant['variant_art_no'] . "<br />";
			
			unset($variant['variant_id']);
			//$method = 'addProductVariant';
			$method = 'addProductMultiVariant';
			
			$variant = $this->prepareProduct($variant);
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			/*
			$validAttributes = NULL;
			if (isset($this->connection->mapping['attribute'])) {
	            $validAttributes = array_keys($this->connection->mapping['attribute']);
	        }
			*/
			
			$count = 1;
			$variant_definitions = array();
			
			
			if (isset($variant['attributes']) && is_array($variant['attributes']) && sizeof($variant['attributes'])){
				foreach ($variant['attributes'] as $attribute){
					if ($attribute['value'] !== '' && $attribute['name'] != ''){
						$variant['label'] = $attribute['name'];
						$variant['value'] = $attribute['value'];
						
						if (isset($this->available_variant_labels[$attribute['name']]) && $this->available_variant_labels[$attribute['name']] != ''){
							$variant['label'] = $this->available_variant_labels[$attribute['name']];
							$attribute['name'] = $this->available_variant_labels[$attribute['name']];
						}
						
						$variant[$attribute['name']] = $attribute['value'];
						
						if ($definitions['variant_' . $count] == $attribute['name']){
							$variant["variation" . $count . "_type"] = $attribute['name'];
							$variant["variation" . $count . "_value"] = $attribute['value'];
							
							$count++;
						}
					}
				}
			}
			unset($variant['attributes']);
			
			//echo "adding variant " . print_r($variant, true) . "<br /><br />";
			
			$variant['product_art_no'] = $product_art_no;
			$variant['key'] = $this->key;
			
			$response = parent::doRequest($url, $post, $variant);
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false;			
		}

		function addProductVariantDefinition($definitions, $product_art_no){
			$method = 'addProductVariantDefinition';
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			
			$definitions['product_art_no'] = $product_art_no;
			$definitions['key'] = $this->key;
			
			$response = parent::doRequest($url, $post, $definitions);
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false;
		}	
		
		function editProductVariant($product_art_no, $variant, $definitions = array()){			
			//echo "EDITING PRODUCT VARIANT: ". $variant['variant_art_no'] . "<br />";
			
			unset($variant['variant_id']);
			//$method = 'editProductVariant';
			$method = 'editProductMultiVariant';
			
			$variant = $this->prepareProduct($variant);
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			/*
			$validAttributes = NULL;
			if (isset($this->connection->mapping['attribute'])) {
	            $validAttributes = array_keys($this->connection->mapping['attribute']);
	        }
			*/
			
			$count = 1;
			$variant_definitions = array();
			
			
			if (isset($variant['attributes']) && is_array($variant['attributes']) && sizeof($variant['attributes'])){
				foreach ($variant['attributes'] as $attribute){
					if ($attribute['value'] !== '' && $attribute['name'] != ''){
						$variant['label'] = $attribute['name'];
						$variant['value'] = $attribute['value'];
						
						if (isset($this->available_variant_labels[$attribute['name']]) && $this->available_variant_labels[$attribute['name']] != ''){
							$variant['label'] = $this->available_variant_labels[$attribute['name']];
							$attribute['name'] = $this->available_variant_labels[$attribute['name']];
						}
						
						$variant[$attribute['name']] = $attribute['value'];
						
						if ($definitions['variant_' . $count] == $attribute['name']){
							$variant["variation" . $count . "_type"] = $attribute['name'];
							$variant["variation" . $count . "_value"] = $attribute['value'];
							
							$count++;
						}
					}
				}
			}
			unset($variant['attributes']);
			unset($variant['images']);
			unset($variant['categories']);
			
			$variant['product_art_no'] = $product_art_no;
			$variant['key'] = $this->key;
			
			$response = parent::doRequest($url, $post, $variant);
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false;
		}
		
		function deleteProductVariantByID($variant_id){
			$method = 'deleteProductVariant';
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			
			$response = parent::doRequest($url, $post, array(
				'key' => $this->key, 
				'variant_id' => $variant_id
			));
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false;
		}
		
		function deleteProductVariantByArtID($variant_art_id){
			$method = 'deleteProductVariant';
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			
			$response = parent::doRequest($url, $post, array(
				'key' => $this->key, 
				'variant_art_id' => $variant_art_id
			));
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false;
		}
		
		
		
		
		/* Product attributes API methods */
		function addProductAttribute($product_art_no, $attribute){
			if ($attribute['name'] == '' || $attribute['value'] == ''){
				$this->error = "No 'name' or 'value' for this attribute";
				//echo "Attribute error: " . $this->error . "<hr />";
				
				return false;
			}
			
			$method = 'addProductAttribute';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			if (isset($this->available_variant_labels[$attribute['name']]) && $this->available_variant_labels[$attribute['name']] != '')
				$attribute['name'] = $this->available_variant_labels[$attribute['name']];
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no, 
				'title' => $attribute['name'],
				'value' => $attribute['value']
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}

		function editProductAttribute($product_art_no, $attribute){
			$method = 'editProductAttribute';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			if (isset($this->available_variant_labels[$attribute['name']]) && $this->available_variant_labels[$attribute['name']] != '')
				$attribute['name'] = $this->available_variant_labels[$attribute['name']];
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no, 
				'title' => $attribute['name'],
				'value' => $attribute['value']
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		function getProductAttributes($product_art_no){
			$method = 'getProductAttributes';
			
			$post = 0;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no
			);
			
			//echo $url . '?' . http_build_query($request_params);
			
			$result = parent::doRequest($url, $post, $request_params);
			$attributes = false;
			
			if ($result['success'] == 1){
				if (count($result['attributes']) > 0){
					$attributes = array();
						
					if (!isset($result['attributes']['attribute'][0])){
						$attributes[] = $result['attributes']['attribute'];
					}
					else {
						foreach ($result['attributes']['attribute'] as $attribute){
							$tmp_attribute = array(
								'attribute_id' => $attribute['attribute_id'],
								'title' => $attribute['title'], 
								'name' => $attribute['title'],
								'value' => $attribute['value'],
							);
							
							array_push($attributes, $tmp_attribute);							
						}						
					}
				}				
			}
			
			return $attributes;			
		}
		
		function deleteProductAttribute($product_art_no, $attribute){
			$method = 'deleteProductAttribute';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			if (isset($this->available_variant_labels[$attribute['name']]) && $this->available_variant_labels[$attribute['name']] != '')
				$attribute['name'] = $this->available_variant_labels[$attribute['name']];
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no, 
				'title' => $attribute['name'],
				'attribute_id' => $attribute['attribute_id']
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		/* Product to Categories API methods */
		function addProductToShopCategory($product_art_id, $external_category_id){
			// possible post params for association (going with fresh product_art_no  and external_shop_category_id)
			// ..............................				
			// shop_category_id 
			// external_shop_category_id 
			// product_id
			// product_art_no
			
			$method = 'addProductToShopCategory';
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			
			$request_params = array(
				'key' => $this->key, 
				'external_shop_category_id' => $external_category_id, 
				'product_art_no' => $product_art_id
			);
			
			$response = parent::doRequest($url, $post, $request_params);
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false; 
		}
		
		function deleteProductFromShopCategory($product_art_id, $external_category_id){
			// possible post params for association (going with fresh product_art_no  and external_shop_category_id)
			// ..............................				
			// shop_category_id 
			// external_shop_category_id 
			// product_id
			// product_art_no
			
			$method = 'deleteProductFromShopCategory';
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$post = 1;
			
			$response = parent::doRequest($url, $post, array(
				'key' => $this->key, 
				'external_shop_category_id' => $external_category_id, 
				'product_art_no ' => $product_art_id
			));
			
			if ($response['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($response);
			
			return false; 
		}
		
		function getProductCategories($product_art_id){
			$method = 'getProductCategories';
			
			$post = 0;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_id
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			$categories = false;
			
			if ($result['success'] == 1){
				if (isset($result['categories']['shop']['category']) && count($result['categories']['shop']['category']) > 0)
					$categories = $result['categories']['shop']['category'];
			}
			
			$etron_categories_ids = false;
			
			if ($categories){
				$CategoryObj = new \Rakuten\Handlers\Categories();
				$existing_categories = $CategoryObj->getExistingCategories();
				
				foreach ($categories as $product_category){
					foreach ($existing_categories as $etron_category){
						if (isset($product_category['category_id'])){
							if ($product_category['category_id'] == $etron_category['shop_category_id'])
								$etron_categories_ids[] = $etron_category['external_shop_category_id'];
						}
						else {
							if ($product_category == $etron_category['shop_category_id'])
								$etron_categories_ids[] = $etron_category['external_shop_category_id'];
						}
					}
				}
			}
			
			return $etron_categories_ids;
		}

		function getProductCrossSellings($product_art_no){
			$method = 'getProductCrossSellings';
			
			$post = 0;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no 	
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			$cross_sellings = false;
						
			if ($result['success'] == 1){
				$cross_sellings = array();
				
				foreach ($result['cross_sellings'] as $product){
					if (!is_array($product))
						$cross_sellings[] = $product;
					else {
						foreach ($product as $pr)
							$cross_sellings[] = $pr;
					}
				}
				return $cross_sellings;
			}
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		
		function deleteProductCrossSelling($product_art_no, $cross_selling_product_art_no){
			$method = 'deleteProductCrossSelling';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no, 
				'cross_selling_product_art_no' => $cross_selling_product_art_no,				
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		function addProductCrossSelling($product_art_no, $cross_selling_product_art_no){
			if (trim($cross_selling_product_art_no) == '')
				return true;
			
			$method = 'addProductCrossSelling';
			
			$post = 1;
			
			$url = str_replace('{group}', $this->group, $this->url);
			$url = str_replace('{method}', $method, $url);
			
			$request_params = array(
				'key' => $this->key, 
				'product_art_no' => $product_art_no, 
				'cross_selling_product_art_no' => $cross_selling_product_art_no, 
				
			);
			
			$result = parent::doRequest($url, $post, $request_params);
			
			if ($result['success'] == 1)
				return true;
			
			$this->error = $this->getErrorMessage($result);
			
			return false; 
		}
		
		function updateProductCrossSellings($product_data, $product_art_no){
			$has_existing_cross_sellings = false;
			if (isset($product_data['existing_cross_sellings']) && is_array($product_data['existing_cross_sellings']) && sizeof($product_data['existing_cross_sellings']))
				$has_existing_cross_sellings = true;
			
			
			if ($has_existing_cross_sellings){
				foreach($product_data['existing_cross_sellings'] as $old_cross_selling){
					$cs_product = $this->getProductByID($old_cross_selling);
					$old_cross_selling = $cs_product['product_art_no'];
			
					if (!in_array($old_cross_selling, $product_data['cross_sellings']))
						$this->deleteProductCrossSelling($product_art_no, $old_cross_selling);
				}
			}
			
			foreach($product_data['cross_sellings'] as $new_cross_selling){
				if	($has_existing_cross_sellings){
					if (!in_array($new_cross_selling, $product_data['cross_sellings']))
						$this->addProductCrossSelling($product_art_no, $new_cross_selling);	
				}
				else 
					$this->addProductCrossSelling($product_art_no, $new_cross_selling);
			}			
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
