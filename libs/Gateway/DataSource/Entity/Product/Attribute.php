<?php

namespace Gateway\DataSource\Entity\Product;

use Gateway\DataSource\Entity\Localized;

/**
 * Product attribute wrapper.
 *
 * @author Lukas Bruha
 */
class Attribute extends Localized implements \Gateway\Mapping\IMappable {
     
    protected $code;
    
    protected $label;
    
    protected $value;
    
    public function __construct($lang, $code, $value = null, $label = "") {
        parent::__construct($lang);
        
        $this->code = $code;
        $this->value = $value;
        $this->label = $label;
    }
    
    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
    
    // MAPPING
    public function getMappingType() {
        return \Gateway\Mapping\IRule::TYPE_ATTRIBUTE;
    }
 
    public function getMappingValue() {
        return $this->getValue();
    }
    
    public function getMappingName() {
        return $this->getCode();
    }
    
    public function setMappingValue($value) {
        $this->setValue($value);
    }
    
    public function setMappingName($name) {
        $this->setCode($name);
    }
    
    
}