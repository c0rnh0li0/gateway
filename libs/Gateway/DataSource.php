<?php

namespace Gateway;

/**
 * General DataSource encapsulates data.
 *
 * @author Lukas Bruha
 */
class DataSource extends \Yourface\Iterators\SimpleIterator implements \Gateway\IDataSource  {
 
    /**
     * Adds new entity into DS.
     * 
     * @param \Gateway\DataSource\IEntity
     */        
    public function add(\Gateway\DataSource\IEntity $entity) {
        $this->data[$entity->key] = $entity;
    }
    
    /**
     * Removes entity if exists.
     * 
     * @param \Gateway\DataSource\IEntity
     */        
    public function remove(\Gateway\DataSource\IEntity $entity) {
        if (!isset($this->data[$entity->key])) {
            throw new \Nette\MemberAccessException("Entity having key of '%s' does not exist in datasource.");
        }
        
        unset($this->data[$entity->key]);
    }
  
}