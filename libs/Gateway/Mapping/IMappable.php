<?php

namespace Gateway\Mapping;

/**
 * Mappable interface must have every entity that allows mapping.
 * 
 * @author Lukas Bruha
 */
interface IMappable {

    /**
     * Returns mapping type (attribute, localization etc.)
     * 
     * @return string
     */        
    public function getMappingType();

    /**
     * Returns mapping value.
     * 
     * @return mixed
     */        
    public function getMappingValue();

    /**
     * Returns name/code of mapping entity.
     * 
     * @return string
     */        
    public function getMappingName();

    /**
     * Sets mapping value.
     * 
     * @param mixed $value
     */        
    public function setMappingValue($value);

    /**
     * Sets name/code of mapping entity.
     * 
     * @param string $name
     */        
    public function setMappingName($name);
    
}
