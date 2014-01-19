<?php

namespace Gateway\DataSource\Entity;

use Gateway\DataSource\IEntity;

/**
 * Description of Entity
 *
 * @author lbruha
 */
class Customer extends \Gateway\DataSource\Entity\Person implements ICustomer, IEntity {

    /**
     * Email.
     *
     * @var string
     */
    protected $email;

    /**
     * Password.
     *
     * @var string
     */
    protected $password;

    /**
     * Addresses.
     *
     * @var array
     */
    protected $addresses = array();

    /**
     * Is enabled?
     *
     * @var bool
     */
    protected $isEnabled = true;

    /**
     * cardID.
     *
     * @var string
     */
    protected $cardID;

    /**
     * filiale.
     *
     * @var string
     */
    protected $filiale;

    /**
     * VAT.
     *
     * @var float
     */
    protected $vat;    
    
    /**
     * Bonus points.
     *
     * @var string
     */
    protected $bonus_value;

    /**
     * Bonus date.
     *
     * @var string
     */
    protected $bonus_date;	

    /**
     * Return Bonus.
     * 
     * @return string
     */
    public function getBonus_value() {
        return $this->bonus_value;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setBonus_value($bonus_value) {
        $this->bonus_value = $bonus_value;
    }    

    /**
     * Return Bonus.
     * 
     * @return string
     */
    public function getBonus_date() {
        return $this->bonus_date;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setBonus_date($bonus_date) {
        $this->bonus_date = $bonus_date;
    }

	
    /**
     * Return property.
     * 
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Adds address.
     * 
     * @param \Gateway\DataSource\Entity\Customer\Address 
     */
    public function addAddress(\Gateway\DataSource\Entity\Customer\Address $address) {
        // FIXME check billing and shipping to have just once
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Return property.
     * 
     * @return array
     */
    public function getAddresses() {
        return $this->addresses;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function isEnabled() {
        return $this->isEnabled;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsEnabled($isEnabled = true) {
        $this->isEnabled = $isEnabled;
    }

    /**
     * Return property.
     * 
     * @return int
     */
    public function getVat() {
        return $this->vat;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setVat($vat) {
        $this->vat = $vat;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getCardID() {
        return $this->cardID;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setCardID($cardID) {
        $this->cardID = $cardID;
    }  

    /**
     * Return property.
     * 
     * @return string
     */
    public function getFiliale() {
        return $this->filiale;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setFiliale($filiale) {
        $this->filiale = $filiale;
    }     
    
    /**
     * Return property key.
     * 
     * @return string
     */
    public function getKey() {
        return $this->getEmail();
    }

}
