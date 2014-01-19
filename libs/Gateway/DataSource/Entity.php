<?php

namespace Gateway\DataSource;

/**
 * Abstract entity class wrapping base properties and methods.
 * 
 * @author Lukas Bruha
 * 
 */
abstract class Entity extends \Nette\Object {
    
    /**
     * Special properties that are not general but used 
     * eg. only in specific datasource.
     *
     * @var array
     */
    protected $specialProperties = array();

    /**
     * Returns property according to given key.
     *
     * @param string
     * @return mixed
     */
    public function getSpecialProperty($key = false) {
        if ($key && isset($this->specialProperties[$key])) {
            return $this->specialProperties[$key];
        }

        return false;
    }
    
    /**
     * Returns all special entities.
     *
     * @return array
     */
    public function getSpecialProperties() {
        return $this->specialProperties;
    }

    /**
     * Adds sspecial property to list.
     * 
     * @param string
     * @param mixed
     * @return \Gateway\DataSource\IEntity
     */
    public function addSpecialProperty($key, $value) {
        if (isset($this->specialProperties[$key])) {
            throw new \Nette\InvalidArgumentException(sprintf("Special property of '%s' already exists.", $key));
        }

        $this->specialProperties[$key] = $value;

        return $this;
    }

}
