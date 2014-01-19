<?php
namespace Gateway\Handler\Erp\Etron\XML\Reader;

use Gateway\Handler\Erp\Etron\XML\Reader,
    Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Customer,
    Gateway\DataSource\Entity\Customer\Address,
    Gateway\DataSource\Entity\Person;

/**
 * Customers XML to DataSource handler.
 *
 * @author Lukas Bruha
 */
class Customers extends Reader {
    
    protected $type = IHandler::TYPE_CUSTOMERS;

     /**
     * Converts XML to DataSource.
     * 
     */
    public function processXml($xml) {
        $ds = new \Gateway\DataSource\Customers();
        
        Utils::log("Processing customers XML input...");
 
        foreach ($xml->customers as $xmlCustomer) {
            // customer entity
            $customer = new Customer();

            $customer->id = (int) $xmlCustomer['customers_id'];
            $customer->firstname = (string) $xmlCustomer['customers_firstname'];
            $customer->surname = (string) $xmlCustomer['customers_lastname'];
            $customer->email = (string) $xmlCustomer['customers_email_address'];
            $customer->password = (string) $xmlCustomer['customers_email_address'];
            $customer->gender = $this->createGender((string) $xmlCustomer['customers_gender']);

            $customer->addSpecialProperty('store_id', 0);
            $customer->addSpecialProperty('website_id', 0);
            $customer->addSpecialProperty('group_id', 0);
            
			//bonus points
			$customer->bonus_value = (int) $xmlCustomer['customers_bonus_value'];
			$customer->bonus_date = (string) $xmlCustomer['customers_bonus_date'];            
            
            //customers members card
            if (!empty($xmlCustomer['customers_card']))
            $customer->cardID = (string) $xmlCustomer['customers_card'];             
            
            // address according to the last XML description - email from Fritz 18.06.2013 v11
            
            if (!empty($xmlCustomer['customers_street_address']) 
                && !empty($xmlCustomer['customers_postcode'])
                && !empty($xmlCustomer['customers_city'])
                && !empty($xmlCustomer['customers_country'])
            )
            {            
                $address = new Address();

                $address->firstname =   (string) $xmlCustomer['customers_firstname'];
                $address->surname =     (string) $xmlCustomer['customers_lastname'];
                $address->street =      (string) $xmlCustomer['customers_street_address'];                       
                $address->postCode =    (string) $xmlCustomer['customers_postcode'];
                $address->city =        (string) $xmlCustomer['customers_city'];
                $address->phoneNo =     (string) $xmlCustomer['customers_telephone'];          
                $address->country =     (string) $xmlCustomer['customers_country']; // FIXME have conversion map/mapping table for this?
                
                // missing in XML
                $address->phoneNo = (string) '123456';            
                $address->setIsBilling(true);
                $address->setIsShipping(false);
                
                $customer->addAddress($address);            
            }
            
            /* NOT USED ANYMORE - see above 
            
            // address entities
            $xmlCustomerAddressesPattern = '//address_book[@customers_id=' . $customer->id . ']';
            $xmlCustomerAdresses = $xml->xpath($xmlCustomerAddressesPattern);            			
			
            if (count($xmlCustomerAdresses)) {
                foreach ($xmlCustomerAdresses as $xmlCustomerAddress) {
                    $address = new Address();
                    
                    $address->firstname = (string) $xmlCustomerAddress['entry_firstname'];
                    $address->surname = (string) $xmlCustomerAddress['entry_lastname'];
                    $address->gender = $this->createGender((string) $xmlCustomerAddress['entry_gender']);
                    $address->postCode = (string) $xmlCustomerAddress['entry_postcode'];
                    $address->city = (string) $xmlCustomerAddress['entry_city'];
                    $address->street = (string) $xmlCustomerAddress['entry_street_address'];
                    
                    // missing in XML
                    $address->phoneNo = (string) '123456';
                    $address->country = (string) 'CZ'; // FIXME have conversion map/mapping table for this?

                    $address->setIsBilling(false);
                    $address->setIsShipping(false);
                    
                    $customer->addAddress($address);
                }
            } else {
                Utils::log(sprintf("Customer '%s' has no address.", $customer->fullName));
            }
            */
            
            //dump($xmlCustomer);
            //dump($customer);
           
            //exit;
            
            $ds->add($customer);            
        }
        
        Utils::log("%s customer has been parsed.", $ds->count());
        
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
            if (!count($xml->customers)) {
                Utils::log("Invalid input format. XML does not contain required 'customers' element.");
                throw new \Nette\IOException("Invalid input format. XML does not contain required 'customers' element.");
            }
              
        } 
        
        return $xml;  
    }

    
    /**
     * Re-maps gender value.
     * 
     * FIXME mapping table
     * 
     * @param string $value
     */
    protected function createGender($value) {
        switch ($value) {
            case "m":
                return Person::TYPE_GENDER_MALE;
                break;
            case "f":
                return Person::TYPE_GENDER_FEMALE;
                break;
            case "c":
                return Person::TYPE_GENDER_UNDEFINED;
                break;
            default:
                return null;
                break;
        }
    }
    
}