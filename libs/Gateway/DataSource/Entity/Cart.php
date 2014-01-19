<?php

namespace Gateway\DataSource\Entity;

/**
 * Cart entity - just datasource with entities.
 * 
 * @author Lukas Bruha
 */
class Cart extends \Gateway\DataSource {

    /**
     * Id.
     *
     * @var int
     */
    protected $id;
    
    /**
     * Overriden due to avoiding having SKU as key.
     * 
     * @param \Gateway\DataSource\IEntity $entity
     */
    public function add(\Gateway\DataSource\IEntity $entity) {
        $this->data[] = $entity;
    }

    /**
     * Overriden due to avoiding having SKU as key.
     * 
     * @param \Gateway\DataSource\IEntity $entity
     * @throws \Nette\MemberAccessException
     */
    public function remove(\Gateway\DataSource\IEntity $entity) {
        throw new \Nette\NotImplementedException();
    }        

    /**
     * Return property.
     * 
     * @return int 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setId($id) {
        $this->id = $id;
    }

}