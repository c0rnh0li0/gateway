<?php

namespace Gateway\Handler\Shop\Rakuten\API\Reader;

use Gateway\Handler\Shop\Rakuten\API\Reader,
	Gateway\IHandler,
    Gateway\Utils,
	Rakuten\Handlers;

/**
 * Orders array/JSON to DataSource handler.
 *
 * @author Lukas Bruha
 */
class Orders extends Reader {

    protected $type = IHandler::TYPE_ORDERS;

    /**
     * Converts array/JSON to DataSource.
     * 
     */
    public function processInput($input) {
        $ds = new \Gateway\DataSource\Orders();

    	Utils::log("Processing orders XML input...");
		require_once(WWW_DIR . "/../libs/Rakuten/Handlers/Orders.php");
		
		$domain = $this->options->get('domain');
        $key = $this->options->get('key');
		
		$Orders = new \Rakuten\Handlers\Orders($key, $domain);
		$ds = $Orders->getOrders();
       
       Utils::log("%s orders has been parsed.", count($ds));

       // return $rakuten_orders;
       return $ds;
    }
}