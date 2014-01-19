<?php

namespace Gateway;

/**
 * Connections set.
 *
 * @author Lukas Bruha
 */
class Connections implements \Iterator {

    /**
     * A list of connection entities.
     * 
     * @var array
     */    
    protected $array;
    
    /**
     * Singleton instance.
     * 
     * @var \Gateway\Connections
     */        
    protected static $instance = null;
   
    /**
     * Adds new connection into list.
     * 
     * @param \Gateway\Connection $connection
     */        
    public function add(Connection $connection) {
        $this->array[$connection->getName()] = $connection;
    }
   
    /**
     * Existance checker.
     * 
     * @param string $key
     * @return bool
     */        
    public function has($key) {
        return isset($this->array[$key]) ? true : false;
    }
    
    /**
     * Connection getter.
     * 
     * @param string $key
     * @raturn \Gateway\Connection
     */        
    public function get($key) {
        return $this->has($key) ? $this->array[$key] : false;
    }
    
    /**
     * Rewind pointer to beginning.
     * 
     */        
    public function rewind() {
        reset($this->array);
    }

    /**
     * Returns current connection.
     * 
     * @return \Gateway\Connection
     */        
    public function current() {
        return current($this->array);
    }

    /**
     * Returns current key.
     * 
     * @return string
     */    
    public function key() {
        return key($this->array);
    }

    /**
     * Moves pointer to another item.
     * 
     */        
    public function next() {
        next($this->array);
    }

    /**
     * Returns key existance.
     * 
     * @return bool
     */        
    public function valid() {
        return $this->key() !== null;
    }
    
    /**
     * Returns array of data.
     * 
     * @return array
     */        
    public function getData() {
        return $this->array;
    }
    
    /**
     * Creates instance and returns it.
     * 
     * @return \Gateway\Connections
     */        
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
}

