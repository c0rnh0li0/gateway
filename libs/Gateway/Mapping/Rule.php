<?php

namespace Gateway\Mapping;

use Gateway\Mapping\IRule;

/**
 * Mapping rule definition to be used.
 *
 * @author Lukas Bruha
 */
abstract class Rule extends \Nette\Object implements IRule {

    /**
     * Mapping type name (attribute, localization etc.)
     * 
     * @var string
     */        
    protected $type = null;
    
    /**
     * Old name of attribute to be mapped.
     * 
     * @var string
     */        
    protected $oldName;
    
    /**
     * Old value of attribute to be mapped.
     * 
     * @var mixed
     */        
    protected $oldValue;
    
    /**
     * New name of attribute to be mapped.
     * 
     * @var string
     */        
    protected $newName;
    
    /**
     * New value of attribute to be mapped.
     * 
     * @var mixed
     */        
    protected $newValue;
    
    /**
     * Scope in which mapping is applied (eg. products.attributes).
     * 
     * @var string
     */        
    protected $scope = null;
    
    /**
     * Handler name upon which mappping is used.
     * 
     * @var string
     */        
    protected $handlerType = null;

    /**
     * Creates an instance of mapping rule including old na new values, 
     * handlers specification and scope.
     * 
     * @param array $nameMapping
     * @param array $valueMapping
     * @param string $handlerType
     * @param string $scope
     */        
    public function __construct(array $nameMapping, array $valueMapping = array(), $handlerType = null, $scope = null) {
        if (!$this->type) {
            throw new \Nette\InvalidStateException("Rule type must be specified, empty given.");
        }
        
        $this->scope = $scope;
        $this->handlerType = $handlerType;

        // extracting 
        $oldName = current(array_keys($nameMapping));
        $newName = current($nameMapping);
        $this->setNameMapping($oldName, $newName);

        if (count($valueMapping)) {
            $oldValue = current(array_keys($valueMapping));
            $newValue = current($valueMapping);

            // old value and new value check if defined both
            if (($oldValue && !$newValue) || (!$oldValue && $newValue)) {
                throw new \Nette\IOException("Both 'oldValue' and 'newValue' must be specified when defining mapping rule.");
            }
            
            $this->oldValue = $oldValue;
            $this->newValue = $newValue;
        }
    }

    /**
     * Sets old to new name mapping.
     * 
     * @param string $oldName
     * @param string $newName
     */
    public function setNameMapping($oldName, $newName) {
        $this->oldName = $oldName;
        $this->newName = $newName;
    }

    /**
     * Sets old to new value mapping.
     * 
     * @param mixed $oldValue
     * @param mixed $newValue
     */    
    public function setValueMapping($oldValue, $newValue) {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }

    /**
     * Returns old name.
     * 
     * @return string
     */        
    public function getOldName() {
        return $this->oldName;
    }

    /**
     * Returns old value.
     * 
     * @return mixed
     */        
    public function getOldValue() {
        return $this->oldValue;
    }

    /**
     * Returns new name.
     * 
     * @return string
     */        
    public function getNewName() {
        return $this->newName;
    }

    /**
     * Returns new value.
     * 
     * @return mixed
     */        
    public function getNewValue() {
        return $this->newValue;
    }

    /**
     * Returns type of mapping.
     * 
     * @return string
     */        
    public function getType() {
        return $this->type;
    }
    
    /**
     * Returns scope of mapping.
     * 
     * @return string
     */        
    public function getScope() {
        return $this->scope;
    }

    /**
     * Sets scope of mapping.
     * 
     * @param string $scope
     */        
    public function setScope($scope) {
        $this->scope = $scope;
    }

    /**
     * Returns handler type.
     * 
     * @return string 
     */        
    public function getHandlerType() {
        return $this->handlerType;
    }

    /**
     * Sets handler type.
     * 
     * @param string $handlerType
     */        
    public function setHandlerType($handlerType) {
        $this->handlerType = $handlerType;
    }
    
    /**
     * __toString() conversion
     * 
     * @return string
     */        
    public function __toString() {
        $mask = "%s: %s (%s) => %s (%s)";
        return sprintf($mask, $this->type, $this->oldName, $this->oldValue ? $this->oldValue : '*', $this->newName, $this->newValue ? $this->newValue : '*');
    }
}
