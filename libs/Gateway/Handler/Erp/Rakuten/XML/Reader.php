<?php

namespace Gateway\Handler\Erp\Rakuten\XML;

use Gateway\Utils,
    Gateway\IConnection;

/**
 * Rakuten ERP adapter for reading stored XML.
 *
 * @author Darko Krstev
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
    protected $fileMasks = array('[0-9]*.xml');

    /**
     * Inits directory settings for XML loading.
     * 
     * @throws \Nette\InvalidArgumentException
     */
    public function __construct(\Gateway\Handler\Options $options) {
    	parent::__construct($options);
        
        // configuration loading
        $params = \Nette\Environment::getContext()->params['gateway_rakuten'];
		
        $root = realpath($params['storage']['root']);

        if (!isset($params['storage']['rakuten'])) {
            throw new \Nette\InvalidArgumentException('Rakuten configuration not found in config.neon[params/gateway/storage/rakuten]');
        }

        $this->dirMask = $root . $params['storage']['rakuten']['inputFolderMask'];
        $this->fileMasks = $params['storage']['rakuten']['allowedFileMasks'];
    }

    /**
     * Loads XML files.
     * 
     * @return array
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * Returns directory mask where XML files can be found.
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
    
    /**
     * Converts XML to DataSource.
     * 
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");
        Utils::log("Starting XML to DataSource conversion...");

        $input = $this->getInput();
        
        // pass loaded XML to be converted to general DataSource 
        $this->dataSource = $this->processXml($input);

        $this->isProcessed = true;
    }
    
    /**
     * Validates input and tries to load XML from it.
     * 
     * @param mixed $input
     * @return boolean
     * @throws \Nette\IOException
     */
    public function validate($input) {
        $res = parent::validate($input);
        
        if ($res) {
            // when array given, we extract only last input from it
            // FIXME support array as input or not? currently only the last one
            if (is_array($input)) {
                $input = current(array_reverse($input));
            }
			
			$xml = null;

            // allowed types checking
            if (is_file($input) || ($input instanceof SplFileInfo)) {
                Utils::log(sprintf("Reading XML from file of '%s'", $input));
                $xml = simplexml_load_file($input);
            } elseif ($input instanceof SimpleXMLElement) {
                // XML already loaded
                $xml = $input;
            } else {
                Utils::log("Reading XML from string...");
                $xml = @simplexml_load_string($input);
            }

            // XML has not been loaded
            if (!$xml) {
                Utils::log("Invalid input format. XML is expected.");
                throw new \Nette\IOException("Invalid input format. XML is expected.");
            }
			
            return $xml;
        }
        
        return $res;
    }
}