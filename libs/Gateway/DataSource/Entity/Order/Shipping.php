<?php

namespace Gateway\DataSource\Entity\Order;

/**
 * Order shipping entity.
 * 
 * @author Lukas Bruha
 */
class Shipping extends \Gateway\DataSource\Entity {

    /**
     * Order relation.
     *
     * @var \Gateway\DataSource\Entity\Order
     */
    protected $order;

    /**
     * Amount.
     *
     * @var int
     */
    protected $amount = 0;

    /**
     * Payment method relation.
     *
     * @var \Gateway\DataSource\Entity\Order\Payment\Method
     */
    protected $method;

    /**
     * Description
     *
     * @var string
     */
    protected $description;

    /**
     * Price
     *
     * @var float
     */
    protected $price;

    /**
     * Address relation.
     *
     * @var \Gateway\DataSource\Entity\Address
     */
    protected $address = null;

    public function getOrder() {
        return $this->order;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Order
     */
    public function setOrder($order) {
        $this->order = $order;
    }

    public function getAmount() {
        return $this->amount;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * Return property.
     * 
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets property.
     * 
     * @param float
     */
    public function setPrice($price) {
        $this->price = $price;
    }

}