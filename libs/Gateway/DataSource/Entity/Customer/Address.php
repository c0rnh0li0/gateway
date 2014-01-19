<?php

namespace Gateway\DataSource\Entity\Customer;

class Address extends \Gateway\DataSource\Entity\Address {

    const TYPE_BILLING = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * Is address type of billing?
     *
     * @var bool
     */
    protected $isBilling = false;

    /**
     * Is address type of shipping?
     *
     * @var bool
     */
    protected $isShipping = false;

    /**
     * Customer relation.
     *
     * @var \Gateway\DataSource\Entity\Customer
     */
    protected $customer;

    /**
     * Company relation.
     *
     * @var string
     */
    protected $company;

    /**
     * Return property.
     * 
     * @return string
     */
    public function isBilling() {
        return $this->isBilling;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsBilling($isBilling = true) {
        $this->isBilling = $isBilling;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function isShipping() {
        return $this->isShipping;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsShipping($isShipping = true) {
        $this->isShipping = $isShipping;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Customer
     */
    public function setCustomer($customer) {
        $this->customer = $customer;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setCompany($company) {
        $this->company = $company;
    }

}
