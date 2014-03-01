<?php

namespace Gateway\Handler\Erp\Rakuten\Xml\Writer;

use Gateway\IHandler,
    Gateway\Utils;

/**
 * Orders DataSource to XML handler.
 *
 * @author Darko Krstev
 */
class Orders extends \Gateway\Handler\Erp\Rakuten\XML\Writer {

    protected $type = IHandler::TYPE_ORDERS;

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

        // invalid datasource format
        if (!is_array($this->dataSource)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Customers", get_class($this->dataSource)));
        }

		// write to output
        $dir = $this->getPath() . DIRECTORY_SEPARATOR . 'orders';
        Utils::mkDir($dir);
		
        Utils::log("Processing DataSource to Etron XML...");
            
       	
        
       	foreach ($this->dataSource as $order) {
       		$order['orders_id'] = str_replace('-', '', $order['orders_id']);
			
            Utils::log(sprintf("Processing order: %s", $order['orders_id']));

            // apply mapping of order statuses
            //$this->connection->applyPropertyMapping($order->status, 'order.status', true);
            
            $xml = new \SimpleXMLElement("<root></root>");
            $xmlOrders = $xml->addChild('orders');
           
		    foreach ($order as $order_key => $order_value){
		   			
		   		if (is_array($order_value) && $order_key == 'orders_products'){
		   			foreach ($order_value as $product){
		   				$xmlOrdersProducts = $xml->addChild('orders_products');
		   				foreach ($product as $product_key => $product_value)
							$xmlOrdersProducts->addAttribute($product_key, $product_value);
		   				
		   			}
		   		}
				else {
					$xmlOrders->addAttribute($order_key, $order_value);
				}				
		    }
			
			$filePath = $dir . DIRECTORY_SEPARATOR . $order['orders_id'] . '-' . Utils::generateFilename('xml');
	        //$xml->saveXML($filePath);
	        
	        $dom = dom_import_simplexml($xml)->ownerDocument;
	        $dom->formatOutput = true;
	        
	        if (file_put_contents($filePath, $dom->saveXML())) {                    
	            Utils::log(sprintf("Output 'orders' XML saved to '%s'.", $filePath));
	        } else {
	            $error = sprintf("Unable to save 'orders' XML to '%s'. Check permissions.", $filePath);
	            
	            Utils::log($error);
	            throw new \Nette\IOException($error);
	        }
			
			unset($xml);
		}
        Utils::log(sprintf("Finished with total of %s orders.", count($this->dataSource)));
        
        
        return count($this->dataSource);
    }
    
    
    /**
     * Returns orders path for storing XMLs ready to be processed by Etron.
     * 
     * @return string
     */
    protected function getPath() {
        // configuration loading
        $params = \Nette\Environment::getContext()->params['gateway'];
        $root = realpath($params['storage']['root']);

        if (!isset($params['storage']['etron'])) {
            throw new \Nette\InvalidArgumentException('Etron configuration not found in config.neon[params/gateway/storage/etron]');
        }

        $path = sprintf($root . $params['storage']['etron']['outputFolderMask'], $this->connection->name);
        
        return $path;
    }

}