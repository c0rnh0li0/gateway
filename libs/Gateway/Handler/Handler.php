<?php

namespace Gateway;

/**
 * Abstract class for handlers.
 *
 * @author Lukas Bruha
 */
abstract class Handler extends \Nette\Object {

    /**
     * ERP or SHOP.
     * 
     * @var string 
     */
    protected $nodeType = null;

    /**
     * Products, customers, orders handler type.
     * 
     * @var string 
     */
    protected $type = null;

    /**
     * Reader or writer.
     * 
     * @var string 
     */
    protected $adapterType = null;
    
    /**
     * Input to be processed.
     * 
     * @var mixed 
     */
    protected $input = null;

    /**
     * Connection reference.
     *
     * @var \Gateway\IConnection
     */
    protected $connection;
    
    /**
     * Handler options.
     * 
     * @var \Gateway\Handler\Options 
     */
    protected $options;

    /**
     * Expected/required options for handler.
     * 
     * @var array
     */
    protected $expectedOptions = array();

    /**
     * Sets handler options (credentials, settings etc.) and validates them.
     * 
     * @param \Gateway\Handler\Options $options
     */    
    public function __construct(\Gateway\Handler\Options $options) {
        $this->options = $options;
        $this->validateOptions();        
    }
    
    /**
     * Returns handler type.
     * 
     * @return string
     */        
    public function getType() {
        return $this->type;
    }

    /**
     * Returns node type.
     * 
     * @return string
     */        
    public function getNodeType() {
        return $this->nodeType;
    }

    /**
     * Returns adapter type
     * 
     * @return string
     */        
    public function getAdapterType() {
        return $this->adapterType;
    }

    /**
     * Connection setter.
     * 
     * @param \Gateway\IConnection $connection
     */
    public function setConnection(\Gateway\IConnection $connection) {
        $this->connection = $connection;
        
        return $this;
    }

    /**
     * Returns connection.
     * 
     * @return \Gateway\Connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Validates options according to self::$expectedOptions class variable.
     * 
     * @throws \Nette\InvalidArgumentException
     */
    protected function validateOptions() {
        $options = $this->options;
        
        $expectedKeys = $this->expectedOptions;
        $definedKeys = $options->getKeys();
        $missingKeys = array_diff($expectedKeys, $definedKeys);
        
        // missing keys
        if (count($missingKeys)) {
            $msg = sprintf("Missing required handler options '%s' for '%s'", implode(", ", $missingKeys), get_class($this));
            Utils::log($msg);
            
            throw new \Nette\InvalidArgumentException($msg);
        }
        
        return true;
    }    
    
    
}

