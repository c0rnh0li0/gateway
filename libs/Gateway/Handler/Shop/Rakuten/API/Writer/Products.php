<?php

namespace Gateway\Handler\Shop\Rakuten\API\Writer;

use Gateway\IHandler,
    Gateway\Utils;

/**
 * Orders DataSource to API interface handler.
 * 
 * @author Darko Krstev
 */
class Products extends \Gateway\Handler\Shop\Rakuten\API\Writer {

    protected $type = IHandler::TYPE_PRODUCTS;
    
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
    	$client = $this->getClient();
    	
        Utils::log(">>> " . get_class($this) . " <<<");

        // FIXME check when setting datasource
        if (!$this->dataSource) {
            Utils::log("No datasource given. Skipping...");
            return;
        }

        // invalid datasource format
        //if (!($this->dataSource instanceof \Gateway\DataSource\Stock)) {
        //    throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Stock", get_class($this->dataSource)));
        //}

        Utils::log("Processing DataSource to Magento via SOAP client...");

        try {

		    $skus = array_keys($this->dataSource);
            $allcalls = 0;
			// Break into blocks of 100 because it's TOO efficient
			foreach (array_chunk($skus, 100) as $skuChunk) {
				$stock = $this->getClient()->call('product_stock.list', array($skuChunk));
				if ($stock) {
					$calls = array();
					foreach ($stock as $product) {
						
						$sku = $product['sku'];
                        $productID = $product['product_id'];
						Utils::log(sprintf("Prepared sku %s.", $sku));
                        
                        if (!empty($this->dataSource[$sku]['quantity'])){
                            $qty = $this->dataSource[$sku]['quantity'];
                            $calls[] = array(
                                'product_stock.update', array($sku, array(
                                    'qty' => $qty,
                                    // only in stock if qty is high enough
                                    'is_in_stock' => $qty > 0
                                ))
                            );
                            Utils::log(sprintf("Stock update, new value: %s.", $qty));
                        }
                        
                        if (!empty($this->dataSource[$sku]['price'])){                            
                            $update_price = $this->dataSource[$sku]['price']; 
                            $update_special_price = (is_null($this->dataSource[$sku]['special_price'])) ? "" : $this->dataSource[$sku]['special_price'];
                            $calls[] = array(
                                'catalog_product.update', array($productID , array(
                                    'price' => $update_price,
                                    'special_price' => $update_special_price
                                ))
                            );
                            Utils::log(sprintf("Price update, new value: %s.", $update_price));
                            Utils::log(sprintf("Special price update, new value: %s.", $update_special_price));
                        }
                        
												
					}
					Utils::log(sprintf("Processing %s multicalls", count($calls)));
                    $allcalls = $allcalls + count($calls);
					$this->getClient()->multiCall($calls);
				}
			}		
			
        } catch (\SoapFault $e) {

            // Internal Error means bad flow of order
            Utils::log(sprintf("Error during passing via Magento SOAP API: %s (%s)", $e->getMessage(), $e->getCode()));
            throw $e;
        }

        $count = count($this->dataSource);
        //Utils::log(sprintf("Finished with total of %s stocks, processed %s.", $count, $this->processed));
		Utils::log(sprintf("Finished with total of %s SKU, processed updates %s.", $count, $allcalls));
        
        return $allcalls; //$this->processed;
    }

}

