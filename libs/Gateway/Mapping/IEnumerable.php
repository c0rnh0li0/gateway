<?php

namespace Gateway\Mapping;

/**
 * Special type of mappable interface must have every entity that allows mapping
 * of properties.
 * 
 * @author Lukas Bruha
 */
interface IEnumerable {

    /**
     * Returns mapping value.
     * 
     * @return mixed
     */
    public function getMappingValue();

    /**
     * Sets mapping value.
     * 
     * @param mixed
     */    
    public function setMappingValue($value);
}
