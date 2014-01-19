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
        if (!($this->dataSource instanceof \Gateway\DataSource\Orders)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Customers", get_class($this->dataSource)));
        }

        Utils::log("Processing DataSource to Etron XML...");
            
        $xml = new \SimpleXMLElement("<root></root>");
        
        foreach ($this->dataSource as $order) {
            Utils::log(sprintf("Processing order: %s", $order->id));

            // apply mapping of order statuses
            $this->connection->applyPropertyMapping($order->status, 'order.status', true);
            
            $xmlOrders = $xml->addChild('orders');
            
            $xmlOrders->addAttribute('date_purchased', $order->createdAt);
            $xmlOrders->addAttribute('last_modified', $order->updatedAt);
            $xmlOrders->addAttribute('orders_id', $order->id);            
            $xmlOrders->addAttribute('orders_status', $order->status);
            $xmlOrders->addAttribute('comments', $order->comment);
 
            // customer
            $customer = $order->customer;
            $billing = $order->billing;
            $billingAddress = $billing->address;
            
            $xmlOrders->addAttribute('customers_cid', '');
            $xmlOrders->addAttribute('customers_id', $customer->id);
            $xmlOrders->addAttribute('customers_company', $billingAddress->company);
            $xmlOrders->addAttribute('customers_firstname', $billingAddress->firstname);
            $xmlOrders->addAttribute('customers_lastname', $billingAddress->surname);
            $xmlOrders->addAttribute('customers_gender', $customer->gender);
            $xmlOrders->addAttribute('customers_street_address', $billingAddress->street);
            $xmlOrders->addAttribute('customers_city', $billingAddress->city);
            $xmlOrders->addAttribute('customers_postcode', $billingAddress->postCode);
            $xmlOrders->addAttribute('customers_country', $billingAddress->country);
            $xmlOrders->addAttribute('customers_state', '');
            $xmlOrders->addAttribute('customers_telephone', $billingAddress->phoneNo);
            $xmlOrders->addAttribute('customers_email_address', $customer->email);
            $xmlOrders->addAttribute('customers_vat_id', $customer->vat);
            $xmlOrders->addAttribute('customers_status', $billingAddress->company ? 3 : 2); // type of customer: 2 = end-customer, 3 = company
            $xmlOrders->addAttribute('customers_card', $customer->cardID);
            $xmlOrders->addAttribute('customers_filiale', $customer->filiale);
            
            // billing special attributes
            foreach ($billing->specialProperties as $key => $value) {
                $xmlOrders->addAttribute($key, $value);
            }
            
            // delivery
            $shipping = $order->shipping;
            $shippingAddress = $shipping->address;
            
            $xmlOrders->addAttribute('delivery_company', $shippingAddress->company);
            $xmlOrders->addAttribute('delivery_firstname', $shippingAddress->firstname);
            $xmlOrders->addAttribute('delivery_lastname', $shippingAddress->surname);
            $xmlOrders->addAttribute('delivery_street_address', $shippingAddress->street);
            $xmlOrders->addAttribute('delivery_city', $shippingAddress->city);
            $xmlOrders->addAttribute('delivery_country', $shippingAddress->country);
            $xmlOrders->addAttribute('delivery_gender', $shippingAddress->gender);
            $xmlOrders->addAttribute('delivery_postcode', $shippingAddress->postCode);

            // shipping special attributes
            foreach ($shipping->specialProperties as $key => $value) {
                $xmlOrders->addAttribute($key, $value);
            }
            
            // payment
            $payment = $order->payment;
            
            $old_payment_method = $payment->method;
            $this->connection->applyPropertyMapping($payment->method, 'order.payment.method', true);
            
            if (isset($payment->specialProperties['paymentType']))
            {
                $payment->type = $payment->specialProperties['paymentType'];
            }
            
            Utils::log(sprintf("payment->type: %s", $payment->type));
            Utils::log(sprintf("payment->method: %s", $payment->method));

            // credit cart mapping for Wirecard and Ogone or Payunity
            if ($payment->type == "CCARD" OR $payment->type == "CC")
            {
                $pay_institutiuon = ($payment->specialProperties['financialInstitution'] == 'MC') ? 'Mastercard' :  $payment->specialProperties['financialInstitution'];
                $xmlOrders->addAttribute('payment_class', 'cc'); 
                $xmlOrders->addAttribute('payment_method', 'cc');
                $xmlOrders->addAttribute('cc_type', $pay_institutiuon);
            } else {
                $xmlOrders->addAttribute('payment_class', $payment->method); 
                $xmlOrders->addAttribute('payment_method', $payment->method);
                if (isset($payment->specialProperties['paymentType'])) {
                    $xmlOrders->addAttribute('cc_type', $payment->specialProperties['paymentType']);
                } else {
                    $xmlOrders->addAttribute('cc_type', '');
                }
            }
            
            
            $xmlOrders->addAttribute('currency', $payment->currency);
            $xmlOrders->addAttribute('shipping_class', '');
            $xmlOrders->addAttribute('shipping_method', $shipping->method);
            
            // payment special attributes
            foreach ($payment->specialProperties as $key => $value) {
                $xmlOrders->addAttribute($key, $value);
            }

            // ordered products
            $index = 1;
            
            foreach ($order->cart as $product) {
                
                /*$xmlOrdersProducts = $xml->addChild('orders_products');
                
                $xmlOrdersProducts->addAttribute('orders_products_id', $index++);
                $xmlOrdersProducts->addAttribute('orders_id', $order->id);
                $xmlOrdersProducts->addAttribute('products_id', $product->id);
                $xmlOrdersProducts->addAttribute('products_quantity', $product->quantity);
                $xmlOrdersProducts->addAttribute('products_model', $product->sku);
                $xmlOrdersProducts->addAttribute('products_name', $product->getDescription()->name);
                $xmlOrdersProducts->addAttribute('products_price', $product->price);
                $xmlOrdersProducts->addAttribute('final_price', $product->finalPrice);
                $xmlOrdersProducts->addAttribute('products_tax', $product->tax);
                //$xmlOrdersProducts->addAttribute('allow_tax', (int) $product->isPriceInclTax());
                $xmlOrdersProducts->addAttribute('allow_tax', false); // always false since we are using price, not price_incl_tax
                 */

                // for bundle we define all children products
                if ($product instanceof \Gateway\DataSource\Entity\Product\IComplex) {
                    
                    // bundle is added as standalone product to XML (configurable is not)
                    if ($product instanceof \Gateway\DataSource\Entity\Product\Bundle) {
                        $this->appendProduct($product, $xml, $order->id, $index++);
                        $bundlePos = $index - 1;
                    }
                    
                    // go through all sub products
                    foreach ($product->getAssociated() as $child) {                        
                        // bundle recognition
                        if ($product instanceof \Gateway\DataSource\Entity\Product\Bundle) {                        
                            $orderedProduct = $this->appendProduct($child, $xml, $order->id, $index++);
                            $orderedProduct->addAttribute('bundle_pos', $bundlePos);
                        } else
                        // merge configurable prices
                        if ($product instanceof \Gateway\DataSource\Entity\Product\Configurable) {
                            $child->price = $child->price + $product->price;
                            $child->finalPrice = $child->finalPrice + $product->finalPrice;
                            $child->tax = $product->tax;
                                    
                            $orderedProduct = $this->appendProduct($child, $xml, $order->id, $index++);
                        }
                    }
                } else {
                    // simple is always added to XML
                    $this->appendProduct($product, $xml, $order->id, $index++);
                }           
            }
            
            // summary
            $xmlSummary = $xml->addChild('orders_total');
            $xmlSummary->addAttribute('class', 'ot_total');
            $xmlSummary->addAttribute('orders_id', $order->id);
            $xmlSummary->addAttribute('value', $order->price);
            
        }
        
        Utils::log(sprintf("Finished with total of %s orders.", $this->dataSource->count()));
        
        // write to output
        $dir = $this->getPath() . DIRECTORY_SEPARATOR . 'orders';
        
        Utils::mkDir($dir);
        
        $filePath = $dir . DIRECTORY_SEPARATOR . Utils::generateFilename('xml');
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
        
        return $this->dataSource->count();
    }
    
    protected function appendProduct($product, $xml, $orderId, $productIndex) {
        $xmlOrdersProducts = $xml->addChild('orders_products');

        $xmlOrdersProducts->addAttribute('orders_products_id', $productIndex);
        $xmlOrdersProducts->addAttribute('orders_id', $orderId);
        $xmlOrdersProducts->addAttribute('products_id', $product->id);
        $xmlOrdersProducts->addAttribute('products_quantity', $product->quantity);
        $xmlOrdersProducts->addAttribute('products_model', $product->sku);
        $xmlOrdersProducts->addAttribute('products_name', $product->getDescription()->name);
        $xmlOrdersProducts->addAttribute('products_price', $product->price);
        $xmlOrdersProducts->addAttribute('final_price', $product->finalPrice);
        $xmlOrdersProducts->addAttribute('products_tax', $product->tax);
        //$xmlOrdersProducts->addAttribute('allow_tax', (int) $product->isPriceInclTax());
        $xmlOrdersProducts->addAttribute('allow_tax', 0); // always false since we are using price, not price_incl_tax                
        
        return $xmlOrdersProducts;
    } 
    
    /**
     * Builds tree path of category.
     * 
     * FIXME use mapping tables
     * 
     * @param string $value
     * @return int
     */    
    protected function getGender($value) {
        switch ($value) {
            case \Gateway\DataSource\Entity\Person::TYPE_GENDER_FEMALE:
                return "f";
                break;
            case \Gateway\DataSource\Entity\Person::TYPE_GENDER_MALE:
                return "m";
                break;
            case \Gateway\DataSource\Entity\Person::TYPE_GENDER_UNDEFINED:
                return "c"; // company
                break;
            default:
                return null;
                break;
        }
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