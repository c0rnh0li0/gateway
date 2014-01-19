<?php

namespace Gateway\Handler\Shop\Magento\JSON\Reader;

use Gateway\Handler\Shop\Magento\JSON\Reader,
    Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Customer,
    Gateway\DataSource\Entity\Order,
    Gateway\DataSource\Entity\Cart,
    Gateway\DataSource\Entity\Product,
    Gateway\DataSource\Entity\Person,
    Gateway\DataSource\Entity\IProduct,
    Gateway\DataSource\Entity\Customer\Address;

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

        Utils::log("Processing orders JSON input...");

        $input = array_reverse($input);
        
        /*dump($input);
        exit;*/
        
        // FIXME foreach 
        foreach ($input as $item) {
            $order = new Order();

            if (!isset($item['increment_id'])) {
                Utils::log('Bad JSON format. Skipping...');
                continue;
            }

            /*dump($item);
            exit;*/

            // base
            $order->id = (int) $item['increment_id'];
            $order->createdAt = (string) $item['created_at'];
            $order->updatedAt = (string) $item['updated_at'];
            $order->isActive = true;
            $order->price = $item['base_grand_total'];

            // customer
            $customer = new Customer();
            $customer->id = $item['customer_id'];
            $customer->email = $item['customer_email'];
            $customer->firstname = $item['customer_firstname'];
            $customer->surname = $item['customer_lastname'];
            $customer->gender = $this->createGender($item['customer_prefix']);
            $customer->vat = $item['customer_taxvat'];
            $customer->setIsEnabled(true);
            
            //Customer club membership
            if (isset($item['customers_card']) ) {
                $customer->cardID = $item['customers_card'];                
            }
            if (isset($item['customers_filiale']) ) {
                $customer->filiale = $item['customers_filiale'];
            }             

            $order->customer = $customer;

            // billing
            $billing = new Order\Billing();
            $billing->discountAmount = $item['discount_amount'];
            $billing->price = $item['base_subtotal'];

            // billing address
            if (isset($item['billing_address'])) {
                $arrAddress = $item['billing_address'];

                $address = new Address();
                $address->street = $arrAddress['street'];
                $address->city = $arrAddress['city'];
                $address->country = $arrAddress['country_id'];
                $address->region = $arrAddress['region_id'];
                $address->postCode = $arrAddress['postcode'];
                $address->company = $arrAddress['company'];
                $address->phoneNo = $arrAddress['telephone'];
                $address->setIsBilling();

                // person
                $address->firstname = $arrAddress['firstname'];
                $address->surname = $arrAddress['lastname'];
                $address->gender = $this->createGender($arrAddress['prefix']);

                $billing->order = $order;
                $billing->address = $address;
            }

            $order->billing = $billing;

            // shipping
            $shipping = new Order\Shipping();
            $shipping->amount = $item['base_shipping_amount'];
            $shipping->method = $item['shipping_method'];
            $shipping->description = $item['customer_note'];

            // shipping address
            if (isset($item['shipping_address'])) {
                $arrAddress = $item['shipping_address'];

                $address = new Address();
                $address->street = $arrAddress['street'];
                $address->city = $arrAddress['city'];
                $address->country = $arrAddress['country_id'];
                $address->region = $arrAddress['region_id'];
                $address->postCode = $arrAddress['postcode'];
                $address->company = $arrAddress['company'];
                $address->phoneNo = $arrAddress['telephone'];
                $address->setIsShipping();

                // person
                $address->firstname = $arrAddress['firstname'];
                $address->surname = $arrAddress['lastname'];
                $address->gender = $this->createGender($arrAddress['prefix']);

                // price
                $shipping->price = $item['base_shipping_amount'];

                $shipping->order = $order;
                $shipping->address = $address;
            }

            $order->shipping = $shipping;

            // payment
            if (isset($item['payment'])) {
                $arrPayment = $item['payment'];

                $payment = new Order\Payment();
                $payment->method = $arrPayment['method'];
                $payment->currency = $item['order_currency_code'];
                $payment->amount = $arrPayment['amount_paid'];
                $payment->type = $arrPayment['cc_type'];

                // other than cach payment - credit card payment?
                if ($payment->type) {

                    // we extract all the information about credit card (prefixed cc_)
                    foreach ($arrPayment as $key => $value) {
                        // skip cc_type, already set in $payment->type
                        if (\Nette\Utils\Strings::startsWith($key, 'cc_') && ($key != 'cc_type')) {
                            $payment->addSpecialProperty($key, $value);
                        }
                    }
                }

                // also add serialized additional data 
                if ($arrPayment['additional_data']) {
                    $addData = (array) unserialize($arrPayment['additional_data']);

                    foreach ($addData as $key => $value) {
                        $payment->addSpecialProperty($key, $value);
                    }
                }

                $payment->order = $order;
                $order->payment = $payment;
            }

            $order->status = (string) $item['state'] . "/" . (string) $item['status'];

            // as comment we take last status_history comment if exists
            if (count($item['status_history'])) {
                $statusNew = current(array_reverse($item['status_history']));
                $order->comment = isset($statusNew['comment']) ? $statusNew['comment'] : null;
            }
            
            // cart - just products datasource
            $cart = new Cart();
            $parents = array();
            
            // orders 
            foreach ($item['items'] as $arrProduct) {
                switch ($arrProduct['product_type']) {
                    case IProduct::TYPE_BUNDLE:                        
                        $product = new Product\Bundle();
                        
                        break;
                    case IProduct::TYPE_CONFIGURABLE:
                        $product = new Product\Configurable();
                        
                        break;
                    default:                        
                        $product = new Product\Simple();
                        
                        break;
                }

                // FIXME taken from V1 - discuss prices mapping!!!
                ////////////
                // PRICES //
                //////////////
                $price = $arrProduct['price'];
                $tax = $arrProduct['tax_percent'];
                
                // just SIMPLE product
                /*if (!isset($arrProduct['parent_item_id']) || !$arrProduct['parent_item_id']) {
                    $price = $arrProduct['price'];
                    $tax = $arrProduct['tax_percent'];
                        
                    //echo "CONFIGURABLE price: " . $price . "<br />";                    
                } else {
                    // CONFIGURABLE or BUNDLE
                    if (!isset($parents[$arrProduct['parent_item_id']])) {
                        $msg = "Missing 'parent_item_id' of ordered configurable or bundle product in Order. Price and tax will not be ok.";
                        Utils::log(\Logger\ILogger::ERROR, $msg);
                        Utils::log(\Logger\ILogger::ERROR, "Product: ", print_r($arrProduct, true));
                    } else {
                        if ($arrProduct['product_type'] == IProduct::TYPE_CONFIGURABLE) { 
                            $price = $arrProduct['price'] + $parents[$arrProduct['parent_item_id']]->price;
                            $tax = $parents[$arrProduct['parent_item_id']]->tax;
                        }
                        
                        //echo "SIMPLE price: " . $price . "<br />";
                    }
                }*/

                $product->id = $arrProduct['product_id'];
                $product->sku = $arrProduct['sku'];
                $product->quantity = (int) $arrProduct['qty_ordered'];
                $product->price = $price;
                $product->isPriceInclTax = $arrProduct['price_incl_tax'] > $price ? true : false; // price_incl_tax == price => price is defined including tax
                $product->tax = $tax;
                $product->discount = isset($arrProduct['discount_percent']) ? $arrProduct['discount_percent'] : null;

                // should be equal to $price * $quantity
                $product->finalPrice = isset($arrProduct['row_total']) ? $arrProduct['row_total'] : null;

                $description = new Product\Description();
                $description->name = $arrProduct['name'];

                $product->addDescription($description);

                if ($arrProduct['product_type'] != IProduct::TYPE_SIMPLE) {
                    $parents[$arrProduct['item_id']] = $product;
                    
                    $cart->add($product);
                } else {
                    // SIMPLE that belongs to CONFIGURABLE or BUNDLE
                    if (isset($arrProduct['parent_item_id']) && $arrProduct['parent_item_id']) {
                        if (!isset($parents[$arrProduct['parent_item_id']])) {
                            $msg = "Missing 'parent_item_id' of ordered configurable or bundle product in Order. Cannot set child to parent product.";
                            Utils::log(\Logger\ILogger::ERROR, $msg);
                            Utils::log(\Logger\ILogger::ERROR, "Product: ", print_r($arrProduct, true));
                        } else {
                            $parentProduct = $parents[$arrProduct['parent_item_id']];
                            
                            // complex product
                            Utils::log('Product %s added to parent product %.', $product->sku, $parentProduct->sku);
                            $parentProduct->addAssociated($product);
                        }
                    } else {                    
                        // standalone products
                        $cart->add($product);
                    }
                }
            }

            $order->cart = $cart;

            $ds->add($order);

             /*dump($order->cart);
              exit; */
        }

        Utils::log("%s orders has been parsed.", $ds->count());

        return $ds;
    }

    /**
     * Re-map gender.
     * 
     * @return string
     */
    protected function createGender($value) {
        switch ($value) {
            case "Herr":
                return "m";
                break;
            case "Frau":
                return "f";
                break;
            case "Firma":
                return "c";
                break;
            default:
                return "";
                break;
        }
    }

}