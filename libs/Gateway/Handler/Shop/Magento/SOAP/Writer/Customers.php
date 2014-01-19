<?php

namespace Gateway\Handler\Shop\Magento\SOAP\Writer;

use Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Person;

/**
 * Customers DataSource to SOAP interface handler.
 *
 * @author Lukas Bruha
 */
class Customers extends \Gateway\Handler\Shop\Magento\SOAP\Writer {

    protected $type = IHandler::TYPE_CUSTOMERS;
    
    /**
     * Unique key mask.
     * 
     * @var string
     */        
    private $_addressUniqueHashMask = '%s_%s_%s';
    
    /**
     * Convert DataSource to SOAP.
     * 
     * @return int Number of processed items.
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");
        
        if (!$this->dataSource) {
            Utils::log("No datasource given. Skipping...");
            return;
        }
        
        // invalid datasource format
        if (!($this->dataSource instanceof \Gateway\DataSource\Customers)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Customers", get_class($this->dataSource)));
        } 

        Utils::log("Processing DataSource to Magento via SOAP client...");
        
        try {
            // all existing customers
            $existingCustomers = $this->getClient()->call('customer.list');
            $existingCustomersEmails = array();
            
            foreach ($existingCustomers as $customer) {
                $existingCustomersEmails[$customer['email']] = $customer['customer_id'];
            }
            
            foreach ($this->dataSource as $customer) {
                Utils::log(sprintf("Processing customer: '%s (%s)'.", $customer->fullName, $customer->email));
                
                // 1) CUSTOMER
                $customerArray = array(
                                    /*'email' => $customer->email, */
                                    'firstname' => $customer->firstname, 
                                    'lastname' => $customer->surname, 
                                    /*'password' => $customer->password,
                                    'gender' => $this->createGender($customer->gender),
                                    'website_id' => 1, 
                                    'store_id' => 1, // where customer created an account
                                    'group_id' => 1,*/
									'bonus_value' =>  $customer->bonus_value, // actual bonus points
									'bonus_date' =>  $customer->bonus_date, // bonus points last update
                                    'customers_card' =>  $customer->cardID // actual bonus points
                    );
                
                // FIXME just prepare and execute multiCall
                // customer exists, just update
                $customerId = null;
                
                if (in_array($customer->email, array_keys($existingCustomersEmails))) {
                    $customerId = $existingCustomersEmails[$customer->email];
                    Utils::log(sprintf("Updating customer: '%s ", $customerId));
                    // UPDATE
                    $this->getClient()->call('customer.update', array(
                                                                            'customerId' => $customerId,
                                                                            'customerData' => $customerArray
                                                        ));
                } else {
                    // NO INSERT - we do not import customer to Magento                   
                    //$customerId = $this->getClient()->call('customer.create', array($customerArray));                    
                }
                
                // 2) ADDRESSES - we will ONLY update billing address - definition in email from Fritz 18.06.2013 v11
                if ($customerId) {
                    $existingCustomersAddresses = $this->getClient()->call('customer_address.list', array($customerId));
                    $existingCustomersAddressesHashes = array();
                    $update_billing_address_ID = '';
                    
                    foreach($existingCustomersAddresses as $address) {
                        //$unique = $this->generateAddressHash($address['street'], $address['city'], $address['postcode']); 
                        
                        //$existingCustomersAddressesHashes[$unique] = $address['customer_address_id'];
                        
                        // check if there is a billing address
                        if ($address['is_default_billing'] === true ){
                            $update_billing_address_ID = $address['customer_address_id'];
                        }
                    }
                    
                    /*
                    dump($existingCustomersAddresses);
                    exit;*/
                    
                    foreach ($customer->getAddresses() as $address) {
                        $customerAddress = array(
                            'firstname'  => $address->firstname,
                            'lastname'   => $address->surname,
                            'country_id' => $address->country,
                            //'region_id'  => $address->region,
                            //'region'     => $address->region,
                            'city'       => $address->city,
                            'street'     => $address->street,
                            'telephone'  => $address->phoneNo,
                            'postcode'   => $address->postCode,

                            'is_default_billing'  => $address->isBilling(),
                            //'is_default_shipping' => !$address->isShipping(),
                        );
                        
                        // check if address exists
                        //$unique = $this->generateAddressHash($address->street, $address->city, $address->postCode);
                        
                        // lets decide if update existing or insert new address
                        //if (in_array($unique, array_keys($existingCustomersAddressesHashes))) {
                        if (!empty($update_billing_address_ID)) {
                            //$addressId = $existingCustomersAddressesHashes[$unique];
                            
                            // UPDATE
                            $this->getClient()->call('customer_address.update', array($update_billing_address_ID, $customerAddress));
                            
                            // remove from existing as updated
                            //unset($existingCustomersAddressesHashes[$unique]);
                            
                            Utils::log(sprintf("Address ID '%s' of customer '%s' has been updated.", $update_billing_address_ID, $customer->fullName));
                        } else {
                            // INSERT
                            $this->getClient()->call('customer_address.create', array($customerId, $customerAddress));
                            
                            Utils::log(sprintf("Address of customer '%s' has been inserted.", $customer->fullName));
                        }
                    }
                    
                    // delete addresses not in list
                    // if there was any address left in exisiting, 
                    // it means, that this address was removed on incoming server,
                    // so we have to remove it also
                    /*if (count($existingCustomersAddressesHashes)) {
                        foreach ($existingCustomersAddressesHashes as $hash => $addressId) {
                            // DELETE
                            $this->getClient()->call('customer_address.delete', array($addressId));   
                            
                            Utils::log(sprintf("Address '%s' of customer '%s' has been deleted.", $hash, $customer->fullName));
                        }
                    }*/
                }
            }
        } catch (\SoapFault $e) {            
            Utils::log(sprintf("Error during passing via Magento SOAP API: %s (%s)", $e->getMessage(), $e->getCode()));
            throw $e;
        }
        
        Utils::log(sprintf("Finished with total of %s customers.", $this->dataSource->count()));
        
        return $this->dataSource->count();
    }
    
    /**
     * Generates unique hash code for address. Used for checking existance in update/insert.
     * 
     * @param string $street
     * @param string $city
     * @param string $postCode
     * @return bool
     */
    private function generateAddressHash($street, $city, $postCode) {
        return sprintf($this->_addressUniqueHashMask, $street, $city, $postCode);
    }

    /**
     * Creates/re-maps gender from value.
     * 
     * FIXME mapping table
     * 
     * @param string
     * @return int
     */        
    protected function createGender($value) {
        switch ($value) {
            case Person::TYPE_GENDER_MALE:
                return 1;
                break;
            case Person::TYPE_GENDER_FEMALE:
                return 2;
                break;
            case Person::TYPE_GENDER_UNDEFINED:
                return 3;
                break;
            default:
                return null;
                break;
        }
    }
    
}

