<?php

namespace Gateway\DataSource;

/**
 * Categories DataSource encapsulating category tree.
 *
 * @author Lukas Bruha
 */
class Categories extends \Gateway\DataSource {

    /**
     * Categories tree as list representation.
     *
     * @var array
     */
    protected $list = array();
    
    /**
     * If true, list has already been loaded.
     *
     * @var bool
     */    
    protected $loaded = false;

    /**
     * Copy category entity from tree to list.
     *
     * @param \Gateway\DatSource\Entity\Category
     */    
    protected function treeToList($entity) {
        if ($entity->hasParent() && !$this->inList($entity->getParent()->getKey())) {
            $this->treeToList($entity->getParent());
        }

        $this->list[$entity->getKey()] = $entity;
    }

    /**
     * Checks if entity in list having given key exists.
     *
     * @param string
     * @return \Gateway\DataSource\Entity\Category
     */
    public function inList($key) {
        $this->toList();

        return array_key_exists($key, $this->list);
    }

    /**
     * Searches for entity in list having given key.
     *
     * @param string
     * @return \Gateway\DataSource\Entity\Category
     */ 
    public function getFromList($key) {
        $this->toList();

        if ($this->inList($key)) {
            return $this->list[$key];
        }

        throw new \Nette\AccessMemberException("No such entity in list.");
    }

    /**
     * Tree to list transformation.
     *
     * @return array
     */
    public function toList() {
        if (!count($this->list) && !$this->loaded) {
            $this->loaded = true;

            foreach ($this->getData() as $entity) {
                $this->treeToList($entity);
            }
        }

        return $this->list;
    }

}