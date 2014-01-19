<?php

namespace Gateway\Handler\Shop\Magento\SOAP\Writer;

use Gateway\IHandler,
    Gateway\Utils;

/**
 * Orders DataSource to SOAP interface handler.
 * 
 * NOTICE: Actually only existing order update is implemented,
 * so it is not possible to create new one.
 *
 * @author Lukas Bruha
 */
class Orders extends \Gateway\Handler\Shop\Magento\SOAP\Writer {

    protected $type = IHandler::TYPE_ORDERS;
    
    /**
     * Number of processed orders.
     * 
     * @var int
     */        
    protected $processed = 0;

    /**
     * Passes DataSource to SOAP.
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
        if (!($this->dataSource instanceof \Gateway\DataSource\Orders)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Customers", get_class($this->dataSource)));
        }

        Utils::log("Processing DataSource to Magento via SOAP client...");

        try {

            // check if order exists
            $existingOrders = $this->getClient()->call('sales_order.list');
            $existingOrdersIds = array();

            foreach ($existingOrders as $order) {
                $existingOrdersIds[$order['increment_id']] = $order['status'];
            }

            // putting via SOAP
            foreach ($this->dataSource as $order) {
                Utils::log(sprintf("Processing order: %s", $order->id));

                // FIXME check possible statuses and throw exception?
                //  State code      Status code
                //  new             pending	
                //  pending_payment pending_paypal
                //                  pending_amazon_asp	                
                //  processing      processing
                //  complete        complete
                //  closed          closed	
                //  canceled        canceled
                //  holded          holded

                // FIXME can only update!!!
                if (in_array($order->id, array_keys($existingOrdersIds))) {
                    // apply mapping tables for enumeration

                    $this->connection->applyPropertyMapping($order->status, 'order.status');

                    // was updated?
                    if ((string) $order->status != $existingOrdersIds[$order->id]) {
                        // UPDATE
                        $res = $this->getClient()->call('sales_order.addComment', array(
                            'orderIncrementId' => $order->id,
                            'status' => (string) $order->status,
                            'comment' => 'status changed: ' . $order->status, // FIXME not in XML
                            //'notify' => false, // FIXME not in XML
                        ));

                        $this->processed++;

                        Utils::log(sprintf("Order no. %s has been updated to status %s.", $order->id, $order->status));          
                    } else {
                        Utils::log(sprintf("Order no. %s was not updated - status '%s' was already set before.", $order->id, $order->status));          
                    }
                } else {
                    // INSERT
                    // creating order not possible 
                    Utils::log(\Logger\ILogger::WARNING, sprintf("Order no. %s does not exists. Skipping...", $order->id));
                    continue;
                }
            }
        } catch (\SoapFault $e) {

            // Internal Error means bad flow of order
            Utils::log(sprintf("Error during passing via Magento SOAP API: %s (%s)", $e->getMessage(), $e->getCode()));
            throw $e;
        }

        $count = $this->dataSource->count();
        Utils::log(sprintf("Finished with total of %s orders, processed %s.", $count, $this->processed));
        
        return $this->processed;
    }

}

