<?php

namespace Gateway\Handler\Shop\Magento\JSON;

use Gateway\IConnection,
    Gateway\Utils;

/**
 * An adapter for reading Magento REST API to be passed to DataSource.
 *
 * @author Lukas Bruha
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
        Utils::log("Starting array/JSON to DataSource conversion...");

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
        $res = parent::validate($input);

        if ($res) {
            
            if (\Yourface\Utils\Validators::isJson($input) || is_array($input)) {
                // first we convert JSON to array
               if (\Yourface\Utils\Validators::isJson($input)) {
                   
                   // hack for PHP < 3.4.0
                   if (!defined('JSON_UNESCAPED_UNICODE')) {
                       define('JSON_UNESCAPED_UNICODE', true);
                   }
                   
                   $input = json_decode($input, JSON_UNESCAPED_UNICODE);
               }
               
               // if single request, we convert it to multi
               if (isset($input['increment_id'])) {
                   $input = array($input);
               }   

               return $input;
   
            } else {
                // input is not valid
                Utils::log("Invalid input format. Array or JSON is expected.");
                throw new \Nette\IOException("Invalid input format. Array or JSON is expected.");

            }            
        }

        return $res;
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