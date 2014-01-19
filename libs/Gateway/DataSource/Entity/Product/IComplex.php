<?php

namespace Gateway\DataSource\Entity\Product;

/**
 * Interface supporting children products.
 * 
 * @author Lukas Bruha
 */
interface IComplex {

    public function addAssociated(\Gateway\DataSource\Entity\Product\Simple $product);
    
    public function getAssociated($keys = false);
    
}

