<?php

namespace Gateway\DataSource\Entity\Order\Payment;

/**
 * Method entity - must be enumerable to allow application of mapping rule 
 * for enumeration.
 * 
 * @author Lukas Bruha
 */
class Method extends \Gateway\DataSource\Entity implements \Gateway\Mapping\IEnumerable {

    /**
     * Payment method value.
     *
     * @var string
     */
    protected $value;

    /**
     * Creates method and sets default value.
     * 
     * @param string $value
     */
    public function __construct($value) {
        $this->setValue($value);
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getMappingValue() {
        return $this->getValue();
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setMappingValue($value) {
        $this->setValue($value);
    }

    /**
     * Return entity as string.
     * 
     * @return string
     */
    public function __toString() {
        return (string) $this->getMappingValue();
    }

}