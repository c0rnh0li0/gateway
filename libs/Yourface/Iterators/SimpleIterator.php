<?php

namespace Yourface\Iterators;

/**
 * Simple iterator class.
 *
 * @author Lukas Bruha
 */
abstract class SimpleIterator implements \Iterator {
 
    protected $data = array();
 
    public function rewind() {
        reset($this->data);
    }

    public function current() {
        return current($this->data);
    }

    public function key() {
        return key($this->data);
    }

    public function next() {
        next($this->data);
    }

    public function valid() {
        return $this->key() !== null;
    }
    
    public function count() {
        return count($this->data);
    }
    
    public function get($key) {
        $key = (string) $key;
        
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        
        return null;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
}