<?php

namespace Gateway\DataSource\Entity\Product;

/**
 * Visibility entity - must be enumerable to allow application of mapping rule 
 * for enumeration.
 * 
 * @author Lukas Bruha
 */
class Visibility extends \Gateway\DataSource\Entity implements \Gateway\Mapping\IEnumerable {

    protected $value;

    public function __construct($value) {
        $this->setValue($value);
    }
    
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getMappingValue() {
        return $this->getValue();
    }

    public function setMappingValue($value) {
        $this->setValue($value);
    }
    
    public function __toString() {
        return $this->getMappingValue();
    }

}