<?php

namespace Gateway\Handler\Shop\Rakuten\API\Writer;

use Gateway\IHandler,
	Gateway\Utils,
	Gateway\DataSource\Entity\Product, 
	Rakuten\Handlers;

require_once('../libs/Rakuten/Handlers/Orders.php');

/**
 * Orders DataSource to API interface handler.
 * 
 * @author Darko Krstev
 */
class Orders extends \Gateway\Handler\Shop\Rakuten\API\Writer {

    protected $type = IHandler::TYPE_ORDERS;
    
	protected $Orders;
	public $existing_orders;
	
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
    	Utils::log(">>> " . get_class($this) . " <<<");

		$domain = $this->options->get('domain');
        $key = $this->options->get('key');
		
		$this->Orders = new \Rakuten\Handlers\Orders($key, $domain);
    	$this->Orders->connection = $this->connection;
		
        // FIXME check when setting datasource
        if (!$this->dataSource) {
            Utils::log("No datasource given. Skipping...");
            return;
        }
		
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/gateway/orders.txt', print_r($this->dataSource, true));
		
		foreach ($this->dataSource as $order)
			$this->Orders->updateOrder($order);

        return count($this->dataSource);
    }
}

