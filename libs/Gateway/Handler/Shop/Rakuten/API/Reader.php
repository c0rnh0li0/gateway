<?php

namespace Gateway\Handler\Shop\Rakuten\API;

use Gateway\IConnection,
    Gateway\Utils;

/**
 * An adapter for reading Rakuten API to be passed to DataSource.
 *
 * @author Darko Krstev
 */
class Reader extends \Gateway\Handler\Reader {

    protected $nodeType = IConnection::NODE_SHOP;
    
    protected $isProcessed = false;

    /**
     * Converts XML to DataSource.
     * 
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");
       Utils::log("Starting array to DataSource conversion...");

       $input = $this->getInput();

        // pass loaded XML to be converted to general DataSource 
    	$this->dataSource = $this->processInput($input);

       $this->isProcessed = true;
    }

    /**
     * Validates given input.
     * 
     * @return mixed
     */        
    public function validate($input) {
        //$res = parent::validate($input);

        //if ($res) {
            // if (!is_array($input)){
            	 // Utils::log("Invalid input format. Array is expected.");
               // throw new \Nette\IOException("Invalid input format. Array is expected.");
           // }
        //}

        return true;
    }
    
        
      /**
     * Returns datasource trasformed from XML.
     * 
     * @return \Gateway\DataSource\IDataSource
     */
    public function getDataSource() {
        if (!$this->isProcessed) {
            $this->process();
        }

        return $this->dataSource;
    }

}