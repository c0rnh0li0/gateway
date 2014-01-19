<?php

namespace Gateway\DataSource\Entity\Order;

/**
 * Order payment entity.
 * 
 * @author Lukas Bruha
 */
class Payment extends \Gateway\DataSource\Entity {

    /**
     * Order relation.
     *
     * @var \Gateway\DataSource\Entity\Order
     */
    protected $order;

    /**
     * Payment method relation.
     *
     * @var \Gateway\DataSource\Entity\Order\Payment\Method
     */
    protected $method;

    /**
     * Payment type.
     *
     * @var string
     */
    protected $type;

    /**
     * Currency code
     *
     * @var string
     */
    protected $currency;

    /**
     * Amount
     *
     * @var int
     */
    protected $amount;

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
     * @return \Gateway\DataSource\Entity\Order\Payment\Method
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Order\Payment\Method
     */
    public function setMethod($method) {
        if ($method instanceof \Gateway\DataSource\Entity\Order\Payment\Method) {
            $this->method = $method;
        } else {
            $this->method = new \Gateway\DataSource\Entity\Order\Payment\Method($method);
        }
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * Return property.
     * 
     * @return int
     */
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
    public function getType() {
        return $this->type;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setType($type) {
        $this->type = $type;
    }

}