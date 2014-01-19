<?php

namespace Gateway\DataSource\Entity\Product;

use Gateway\DataSource\Entity\IProduct;

/**
 * Pure Category wrapper.
 *
 * @author Lukas Bruha
 */
class Category extends \Gateway\DataSource\Entity\Category {
    
    protected $products;
    
    public function addProduct(IProduct $product) {
        $this->products[$product->sku] = $product;
        
        return $this;
    }
    
    public function getProducts() {
        return $this->products;
    }
    
}

