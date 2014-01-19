<?php

namespace Gateway\Handler\Erp\Etron\CSV\Reader;

use Gateway\Handler\Erp\Etron\CSV\Reader,
    Gateway\IHandler,
    Gateway\Utils;

/**
 * Products stock CSV to DataSource handler.
 *
 * @author Nikola Badev
 */
class Stock extends Reader {

    protected $type = IHandler::TYPE_STOCK;

    /**
     * Debug helper.
     * 
     * @var array
     */
    protected $debug = array(
                        'limit' => false,
                        'oldCategory' => false,
    );
    
    
    /**
     * If true, null values of attribute does not allow 
     * associated products to be visible.
     * 
     * @var bool
     */
    protected $allowAttributeEmptyValues = false;
    
    /**
     * Parse given CSV filename.
     * 
     * @param \string $csv
     */
    protected function processCsv($csv) {
        Utils::log("Processing skus CSV input...");
		$skus = new \Gateway\DataSource\Stock();
		
		$qtyBySku = array();
		
		$Data = str_getcsv($csv, "\n"); //parse the rows 
		array_shift($Data); 			// first column is ignored (sku,qty,price).
		foreach($Data as &$Row) {		
			$vals = explode(';', $Row); //vals[0] - sku ; vals[1] - qty ; vals[2] - price ; vals[3] - special_price
			$qtyBySku[$vals[0]]['quantity'] = (float) $vals[1];
            $qtyBySku[$vals[0]]['price'] = (float) $vals[2];
            $qtyBySku[$vals[0]]['special_price'] = (!empty($vals[3]) && is_numeric($vals[3])) ? (float) $vals[3] : NULL;
		}
					
        //Utils::log(sprintf("array created from csv '%s'", print_r($qtyBySku)));				
		Utils::log("%s skus has been parsed.", count(array_keys($qtyBySku)));        
        Utils::log("Products DataSource is prepared.");
        return $qtyBySku;
    }
 

    /**
     * Validates input and tries to load CSV from it.
     * 
     * @param mixed $input
     * @return boolean
     * @throws \Nette\IOException
     */
    public function validate($input) {
        $csv = parent::validate($input);
        /*if ($csv) {
            // very simple validation - just search for products inside
            if (!count($csv->products)) {
                Utils::log("Invalid input format. XML does not contain required 'products' element.");
                throw new \Nette\IOException("Invalid input format. CSV does not contain required 'products' element.");
            }    
        } */
        return $csv;  
    }
}