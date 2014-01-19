<?php

namespace Gateway\Handler\Erp\Etron\XML\Reader\Products;

use Gateway\Handler\Erp\Etron\XML\Reader\Products,
    Gateway\IHandler;

/**
 * Products categories XML to DataSource handler.
 *
 * @author Lukas Bruha
 */
class Categories extends Products {

    protected $type = IHandler::TYPE_PRODUCTS_CATEGORIES;
 
    
    protected function loadCategories($xml, $xmlProduct, $product) {
            // searches all current product categories IDs
            $xmlProductCategoriesPattern = '//products_to_categories[@products_id=' . $xmlProduct['products_id'] . ']/@categories_id';
            $productCategoriesIds = $xml->xpath($xmlProductCategoriesPattern);

            if (count($productCategoriesIds)) {
                array_walk($productCategoriesIds, function(&$item) { 
                        $item = (string) $item;
                });
            }
            
            $product->addSpecialProperty("categoryErpIds", $productCategoriesIds);
    }
}