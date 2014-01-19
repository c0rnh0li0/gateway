<?php

namespace Gateway\Handler\Erp\Etron\CSV;

use Gateway\Utils,
    Gateway\IConnection;

/**
 * ETRON ERP adapter for reading stored CSV.
 *
 * @author Nikola Badev
 */
class Reader extends \Gateway\Handler\Reader {
    
    protected $nodeType = IConnection::NODE_ERP;

    /**
     * Flag storing information status of processed file.
     * 
     * @var bool
     */
    protected $isProcessed = false;
    
    /**
     * Mask for directory including files.
     * 
     * @var string
     */
    protected $dirMask;
    
    /**
     * Mask for file allowed to be processed.
     * 
     * @var string
     */
    protected $fileMasks = array('[0-9]*.csv');

    /**
     * Inits directory settings for CSV loading.
     * 
     * @throws \Nette\InvalidArgumentException
     */
    public function __construct(\Gateway\Handler\Options $options) {
        parent::__construct($options);
        
        // configuration loading
        $params = \Nette\Environment::getContext()->params['gateway'];
        $root = realpath($params['storage']['root']);

        if (!isset($params['storage']['etron'])) {
            throw new \Nette\InvalidArgumentException('Etron configuration not found in config.neon[params/gateway/storage/etron]');
        }

        $this->dirMask = $root . $params['storage']['etron']['inputFolderMask'];
        $this->fileMasks = $params['storage']['etron']['allowedFileMasks'];
		
    }

    /**
     * Loads CSV files.
     * 
     * @return array
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * Returns directory mask where CSV files can be found.
     * 
     * @return string
     */
    public function getDirMask() {
        return $this->dirMask;
    }
    
    /**
     * Returns allowed file masks to be parsed in input folder.
     * 
     * @return string
     */
    public function getFileMasks() {
        return $this->fileMasks;
    }
        
    /**
     * Returns datasource trasformed from CSV.
     * 
     * @return \Gateway\DataSource\IDataSource
     */
    public function getDataSource() {
        if (!$this->isProcessed) {
            $this->process();
        }

        return $this->dataSource;
    }
    
    /**
     * Converts CSV to DataSource.
     * 
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");
        Utils::log("Starting CSV to DataSource conversion...");

        $input = $this->getInput();
        
        // pass loaded CSV to be converted to general DataSource 
        $this->dataSource = $this->processCsv($input);

        $this->isProcessed = true;
    }
    
    /**
     * Validates input and tries to load CSV from it.
     * 
     * @param mixed $input
     * @return boolean
     * @throws \Nette\IOException
     */
    public function validate($input) {
        $res = parent::validate($input);
        
        if ($res) {           		
			
            // allowed types checking
            if (is_file($input) || ($input instanceof SplFileInfo)) {
                Utils::log(sprintf("Reading CSV from file of '%s'", $input));
                //$xml = simplexml_load_file($input);
				$csv = file_get_contents($input);
            } elseif (strpos($input,'sku') !== false) {
                // CSV already loaded
                $csv = $input;
            } else {
                Utils::log("Reading CSV from string...");
                //$xml = @simplexml_load_string($input);
				$csv = @file_get_contents($input);
            }

            // CSV has not been loaded
            if (!$csv) {
                Utils::log("Invalid input format. CSV is expected.");
                throw new \Nette\IOException("Invalid input format. CSV is expected.");
            }
            
            return $csv;
        }
        
        return $res;
    }
}