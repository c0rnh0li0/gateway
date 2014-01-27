<?php

	namespace Rakuten\Handlers;
	
	use Rakuten;
	
	class Products extends \Rakuten\Rakuten {
		var $product;
		var $product_images;
		var $product_variants;
		var $product_errors;
		var $product_from_xml;
		var $from_attributes;
		var $product_response;
		
		var $group = "products";
		
		
		function __construct(){
			parent::__construct();
		
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
			
			$this->product_add_errors = array(
				1005 => 'The article number is in the product inventory already exists',
				1010 => 'The ISBN number is invalid',
				1011 => 'The EAN number is invalid',
				1015 => 'The shipping group could not be found',
				1020 => 'The reduced price is not more favorable than the standard price',
				1030 => 'The Rakuten category could not be found',
				1031 => 'The Shop Category was not found'
			);
			
			$this->product_edit_errors = array(
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
				2245 => 'The shipping group could not be found'
			);
			
			$this->product_delete_errors = array(
				2505 => 'The product ID or item number was not transferred',
				2510 => 'The product could not be found'
			);
			
			$this->product_response = array(
				'success' => 1,
				'product_id' => ''
			);
			
			// use array_flip
			$this->product_from_xml = array(
				'product_id' => 'products_id', 						// int: ID of the product
				'product_art_no' => 'products_model',				// FROM ATTRIBUTES // string: Item number
				'name' => 'products_description~name', 	// PRODUCTS_DESCRIPTION - PRODUCTS_NAME // string: Name of product
				'price' => 'products_price|products_regularprice', 	// Float: Price is not present in a product with variations!
				'price_reduced' => 'products_priceb', 				// ????????			// Float: Reduced price is not present in a product with variations!
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
		}	

		function buildProduct($product_xml, $attributes_array){
			$product_data = array();
			$xml_attrs = array_flip($this->product_from_xml);
			
			foreach ($xml_attrs as $key => $value){
				if (!empty($key)){
					if (strstr($key, '~')){
						$tmp = explode('~', $key);
						
						if ($tmp[0] == 'attributes')
							$product_data[$value] = $this->getAttributeValue($tmp[1], $attributes_array['attributes']);
						else 
							$product_data[$value] = (string) $attributes_array[$tmp[0]][$tmp[1]];
					}
					else if (strstr($key, '|')){
						$tmp = explode('|', $key);
						$tmp_value = '';
						if (!empty($product_xml[$tmp[0]]))
							$tmp_value = (string) $product_xml[$tmp[0]];
						else 
							$tmp_value = (string) $product_xml[$tmp[1]];
						
						$product_data[$value] = $tmp_value;
					}
					else 
						$product_data[$value] = (string) $product_xml[$key];
					
				}
				else
					$product_data[$value] = '';
			}

			$product_data['categories'] = $attributes_array['categories'];
			$product_data['images'] = $attributes_array['images'];
			
			return $product_data;
		}

		function getAttributeValue($key, $attributes) {
			foreach ($attributes as $attribute)
				if ($attribute['name'] == $key)
					return $attribute['value'];
		}

		function getProducts($product_model_id = null){
			$method = 'getProducts';
			
			
		}
		
		
		/* Product main API methods */
		function addProduct($product_xml){
			$method = 'addProduct';
		}

		function editProduct(){
			
		}
		
		function deleteProduct(){
			
		}
		
		
		
		/* Product images API methods */
		function addProductImage(){
			
		}
		
		function deleteProductImage(){
			
		}
		
		
		
		/* Product variants API methods */
		function addProductVariant(){
			
		}
		
		function editProductVariant(){
			
		}
		
		function deleteProductVariant(){
			
		}
		
		
		
		/* Product attributes API methods */
		function addProductAttribute(){
			
		}
		
		function getProductAttributes(){
			
		}
		
		function deleteProductAttribute(){
			
		}
	} 
	
?>