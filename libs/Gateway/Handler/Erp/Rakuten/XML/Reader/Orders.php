<?php
namespace Gateway\Handler\Erp\Rakuten\XML\Reader;

use Gateway\Handler\Erp\Rakuten\XML\Reader,
    Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Order;

/**
 * Orders XML to DataSource handler.
 *
 * @author Darko Krstev
 */
class Orders extends Reader {
    
    protected $type = IHandler::TYPE_ORDERS;

     /**
     * Converts XML to DataSource.
     * 
     */
    public function processXml($xml) {
        $ds = new \Gateway\DataSource\Orders();
        
        Utils::log("Processing orders XML input...");
		$orders = array(); 
 		 
        foreach ($xml->orders as $xmlOrder) {
            $order = array();
            
			$order['order_no'] = (int) $xmlOrder['orders_id'];
			$order['status'] = (int) $xmlOrder['orders_status'];
			$orders[] = $order;
        }
        
		Utils::log("%s orders has been parsed.", count($orders));
		
        $ds = $orders;		
        return $ds;
    }

    
    /**
     * Validates input and tries to load XML from it.
     * 
     * @param mixed $input
     * @return boolean
     * @throws \Nette\IOException
     */
    public function validate($input) {
        $xml = parent::validate($input);
        
        if ($xml) {

            // very simple validation - just search for customers inside
            if (!count($xml->orders)) {
                Utils::log("Invalid input format. XML does not contain required 'orders' element.");
                throw new \Nette\IOException("Invalid input format. XML does not contain required 'orders' element.");
            }
              
        } 
        
        return $xml;  
    }
}