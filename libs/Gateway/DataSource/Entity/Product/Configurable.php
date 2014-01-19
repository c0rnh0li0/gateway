<?php

namespace Gateway\DataSource\Entity\Product;

/**
 * Configurable product entity;
 *
 * @author Lukas Bruha
 */
class Configurable extends Complex {
    
    protected $type = self::TYPE_CONFIGURABLE;
    
    /**
     * What attributes to have visible in Configurable (depends on selected and defined attributes set).
     * 
     * @var array 
     */
    protected $visibleAttributes = array();   
    
    public function addVisibleAttribute($code) {
        $this->visibleAttributes[] = $code;
        
        return $this;
    }
    
    public function getVisibleAttributes() {
        return $this->visibleAttributes;
    }
    
    public function setAssociated($associated) {
        $this->associated = $associated;
    }
    
}

