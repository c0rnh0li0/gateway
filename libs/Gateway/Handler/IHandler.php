<?php

namespace Gateway;

/**
 * Handler types interface.
 * 
 * @author Lukas Bruha
 */
interface IHandler {

    const TYPE_PRODUCTS = 'products';
    const TYPE_PRODUCTS_IMAGES = 'products_images';
    const TYPE_PRODUCTS_CATEGORIES = 'products_categories';
    const TYPE_CUSTOMERS = 'customers';
    const TYPE_ORDERS = 'orders';
    const TYPE_CATEGORIES = 'categories';
	const TYPE_STOCK = 'stock';
    
    public function setConnection(\Gateway\IConnection $connection);
    
    /**
     * Returns connection.
     * 
     * @return \Gateway\Connection
     */
    public function getConnection();
    
    /**
     * Returns handler type.
     * 
     * @return string
     */
    public function getType();

    /**
     * Returns node type.
     * 
     * @return string
     */
    public function getNodeType();

    /**
     * Returns adapter type
     * 
     * @return string
     */
    public function getAdapterType();
}

