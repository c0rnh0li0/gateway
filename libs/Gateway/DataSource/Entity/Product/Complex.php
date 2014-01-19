<?php

namespace Gateway\DataSource\Entity\Product;

use Gateway\DataSource\Entity\Product;

/**
 * Complex product entity;
 *
 * @author Lukas Bruha
 */
class Complex extends Product implements IComplex {
    
    /**
     * Simple products children (eg. Shirt L, Shirt XL, ... where parent is Shirt).
     * 
     * @var array
     */
    protected $associated = array();
    
    /**
     * Adds associated products.
     * 
     * @param \Gateway\DataSource\Entity\Product\Simple $product
     * @return \Gateway\DataSource\Entity\Product\IComplexProduct
     */    
    public function addAssociated(Simple $product) {
        $this->associated[$product->sku] = $product;
        $product->setParent($this);
        
        return $this;
    }    
       
    /**
     * Returns associated/child products.
     * If keys param is true, then returns SKUs list only.
     * 
     * @param bool $keys
     * @return array
     */
    public function getAssociated($keys = false) {
        if ($keys && count($this->associated)) {
            return array_keys($this->associated);
        }
        
        return $this->associated;
    }
}

