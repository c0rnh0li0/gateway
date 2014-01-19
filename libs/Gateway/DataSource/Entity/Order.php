<?php

namespace Gateway\DataSource\Entity;

/**
 * Order entity.
 * 
 * @author Lukas Bruha
 */
class Order extends \Nette\Object implements \Gateway\DataSource\IEntity {

    /**
     * ID.
     *
     * @var int
     */
    protected $id;

    /**
     * Created at datetime.
     *
     * @var string
     */
    protected $createdAt;

    /**
     * Updated at datetime.
     *
     * @var string
     */
    protected $updatedAt;

    /**
     * Is active?
     *
     * @var string
     */
    protected $isActive = false;

    /**
     * Customer relation
     *
     * @var \Gateway\DataSource\Entity\Customer
     */
    protected $customer;

    /**
     * Cart relation
     *
     * @var \Gateway\DataSource\Entity\Cart
     */
    protected $cart;

    /**
     * Shipping relation
     *
     * @var \Gateway\DataSource\Entity\Order\Shipping
     */
    protected $shipping;

    /**
     * Billing relation
     *
     * @var \Gateway\DataSource\Entity\Order\Billing
     */
    protected $billing;

    /**
     * Payment relation
     *
     * @var \Gateway\DataSource\Entity\Order\Payment
     */
    protected $payment;

    /**
     * Status relation
     *
     * @var \Gateway\DataSource\Entity\Order\Status
     */
    protected $status;

    /**
     * Price.
     *
     * @var int
     */
    protected $price;

    /**
     * Order comment.
     *
     * @var \Gateway\DataSource\Entity\Order\Shipping
     */
    protected $comment;

    /**
     * Return property.
     * 
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function isActive() {
        return $this->isActive;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsActive($isActive = true) {
        $this->isActive = $isActive;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\ICustomer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\ICustomer
     */
    public function setCustomer(\Gateway\DataSource\Entity\ICustomer $customer) {
        $this->customer = $customer;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Cart
     */
    public function getCart() {
        return $this->cart;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Cart
     */
    public function setCart(\Gateway\DataSource\Entity\Cart $cart) {
        $this->cart = $cart;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Order\Shipping
     */
    public function getShipping() {
        return $this->shipping;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Order\Shipping
     */
    public function setShipping(\Gateway\DataSource\Entity\Order\Shipping $shipping) {
        $this->shipping = $shipping;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Order\Billing
     */
    public function getBilling() {
        return $this->billing;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Order\Billing
     */
    public function setBilling(\Gateway\DataSource\Entity\Order\Billing $billing) {
        $billing->order = $this;

        $this->billing = $billing;
    }

    /**
     * Return property.
     * 
     * @return \Gateway\DataSource\Entity\Order\Payment
     */
    public function getPayment() {
        return $this->payment;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Order\Payment
     */
    public function setPayment(\Gateway\DataSource\Entity\Order\Payment $payment) {
        $payment->order = $this;

        $this->payment = $payment;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Sets property.
     * 
     * @param mixed
     */
    public function setStatus($status) {
        if ($status instanceof \Gateway\DataSource\Entity\Order\Status) {
            $this->status = $status;
        } else {
            // FIXME allow only integer or string
            $this->status = new \Gateway\DataSource\Entity\Order\Status($status);
        }
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
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * Unique key used in datasource.
     * 
     * @return int
     */
    public function getKey() {
        return $this->getId();
    }

}