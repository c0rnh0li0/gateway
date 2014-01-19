<?php

namespace Gateway\Handler;

/**
 * Handler options wrapper.
 * 
 * @author Lukas Bruha
 */
class Options extends \Yourface\Iterators\SimpleIterator implements \Iterator {
    
    /**
     * Array of options.
     * 
     * @var array
     */     
    protected $data = array();

    /**
     * Accepts connection string or array.
     * When string, expected format is a "connection string"
     * like 'dbname=my_db;host=test'
     * 
     * @param array|string $options
     */
    public function __construct($options) {
        $this->load($options);
    }
    
    /**
     * Checks and loads options into data class variable.
     * 
     * @param string $options
     * @throws \Nette\InvalidArgumentException
     */
    protected function load($options) {
        if (is_array($options)) {
            $this->data = $options;
        } elseif ($options) {
            if ($options && !preg_match('/^[a-zA-Z0-9=;_@:\.\-!\*:\/]+$/', $options)) {
                throw new \Nette\InvalidArgumentException(sprintf("Invalid options format for '%s' handler: '%s' given.", get_class($this), $options));
            }
            
            // eg. 'dbname=my_db;host=test' -> array('dbname' => 'my_db', 'host' => 'test')
            preg_match_all("/([^;=]+)=([^;=]*)/", $options, $matches);
            $this->data = array_combine($matches[1], $matches[2]);
        }
    }
    
    /**
     * Returns options keys
     * 
     * @return array
     */    
    public function getKeys() {
        return array_keys($this->getData());
    }
    
    /**
     * Returns data item or the whole data.
     * 
     * @return mixed|array
     */        
    public function getData($key = false) {
        if ($key) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            }
            
            return null;
        }
        
        return $this->data;
    }
    
}