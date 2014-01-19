<?php

namespace Gateway\DataSource\Entity\Order;

/**
 * Order billing entity.
 * 
 * @author Lukas Bruha
 */
class Billing extends \Gateway\DataSource\Entity {

    /**
     * Order relation.
     *
     * @var \Gateway\DataSource\Entity\Order
     */
    protected $order;

    /**
     * Discount in order billing
     *
     * @var float
     */
    protected $discountAmount = 0;

    /**
     * Price.
     *
     * @var float
     */
    protected $price;

    /**
     * Order addres.
     *
     * @var \Gateway\DataSource\Entity\Address
     */
    protected $address;

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Order
     */
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

    /**
     * Return property.
     * 
     * @return int
     */
    public function getDiscountAmount() {
        return $this->discountAmount;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setDiscountAmount($discountAmount) {
        $this->discountAmount = $discountAmount;
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

}