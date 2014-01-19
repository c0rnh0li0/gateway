<?php

namespace Gateway\DataSource\Entity;

/**
 * General address.
 * 
 * @author Lukas Bruha
 */
class Address extends \Gateway\DataSource\Entity\Person {

    /**
     * Country.
     *
     * @var string
     */
    protected $country;

    /**
     * Region.
     *
     * @var string
     */
    protected $region;

    /**
     * City.
     *
     * @var string
     */
    protected $city;

    /**
     * Street.
     *
     * @var string
     */
    protected $street;

    /**
     * Phone number.
     *
     * @var string
     */
    protected $phoneNo;

    /**
     * Post code.
     *
     * @var int
     */
    protected $postCode;

    /**
     * Return property.
     * 
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setStreet($street) {
        $this->street = $street;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getPhoneNo() {
        return $this->phoneNo;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setPhoneNo($phoneNo) {
        $this->phoneNo = $phoneNo;
    }

    /**
     * Return property.
     * 
     * @return int
     */
    public function getPostCode() {
        return $this->postCode;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setPostCode($postCode) {
        $this->postCode = $postCode;
    }

}