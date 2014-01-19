<?php

namespace Gateway\Handler\Shop\Magento\Magmi;

use Gateway\Utils,
    Gateway\IConnection;

require_once(WWW_DIR . "/../libs/Magmi/inc/magmi_defs.php");
require_once(WWW_DIR . "/../libs/Magmi/integration/inc/magmi_datapump.php");
require_once(WWW_DIR . "/../libs/Magmi/inc/magmi_config.php");

/**
 * An adapter for parsing datasource to be passed to Magento e-commerce.
 *
 * @author Lukas Bruha
 */
class Writer extends \Gateway\Handler\Writer {

    protected $nodeType = IConnection::NODE_SHOP;

    /**
     * Magmi instance.
     * 
     * @var \Magmi
     */
    protected $magmi = null;

    /**
     * Used profile in Magmi (see magmi.ini)
     * 
     * @var string
     */
    protected $magmiProfile = 'gateway2';

    /**
     * Overriden due to Magmi Config init.
     * 
     * @param \Gateway\IConnection $connection
     */
    protected $magmiMode = 'create';

    /**
     * Expected options for Magmi handler.
     * 
     * @var array 
     */
    protected $expectedOptions = array('dbname', 'host', 'user', 'password', 'version');

    /**
     * Creates Magmi instance.
     * 
     * @param \Gateway\Handler\Options $options
     */
    public function __construct(\Gateway\Handler\Options $options) {
        parent::__construct($options);

        $this->magmi = \Magmi_DataPumpFactory::getDataPumpInstance("productimport");
    }

    /**
     * Overriden due to Magmi Config init.
     * 
     * @param \Gateway\IConnection $connection
     */
    public function setConnection(\Gateway\IConnection $connection) {
        parent::setConnection($connection);

        $this->_initConfig();

        return $this;
    }

    /**
     * Inits dynamic config.
     * 
     * @throws \Nette\IOException
     */
    private function _initConfig() {
        $config = \Nette\Environment::getContext()->params['gateway']['magmi']['config'];
        $magmiConfig = \Magmi_Config::getInstance();

        // MAGMI configuration update
        $this->_setConfigProperty();

        // generates magmi.ini for connection 'on the fly'
        $arr = array();

        // read default settings from config and also merge with options
        foreach ($config['sections'] as $key => $group) {
            $key = strtoupper($key);

            // creating array of KEY:name = value
            foreach ($group as $name => $value) {
                $dynamicValue = $this->options->get($name);

                // if options for handler includes this variable, 
                // replace default one
                if ($dynamicValue) {
                    $value = $dynamicValue;
                }

                // KEY:name = value format
                $composedKey = $key . ":" . $name;
                $arr[$composedKey] = $value;
            }
        }


        $arr["DATABASE:host"] = $this->options->get('host');
        $arr["DATABASE:user"] = $this->options->get('user');
        $arr["DATABASE:password"] = $this->options->get('password');
        /* dump($this->options);
          dump($config['sections']);
          dump($arr);
          exit; */

        if (!$magmiConfig->save($arr)) {
            throw new \Nette\IOException("Error during saving Magmi configuration in magmi.ini. Check folder permissions or defined values.");
        }
    }

    /**
     * Sets current config property to be used.
     * 
     */
    private function _setConfigProperty() {
        $config = \Nette\Environment::getContext()->params['gateway']['magmi']['config'];

        $magmiConfig = \Magmi_Config::getInstance();

        // MAGMI LIB FIX - a re-set of private property of Magmi_Config
        if (isset($config['dir']) && $config['dir']) {
            $connName = $this->getConnection()->getName();
            $connMagmiConfigDir = $config['dir'] . DIRECTORY_SEPARATOR . $connName;

            Utils::mkDir($connMagmiConfigDir);
            $configPath = $connMagmiConfigDir . DIRECTORY_SEPARATOR . 'magmi.ini';

            $refObject = new \ReflectionObject($magmiConfig);
            $refProperty = $refObject->getProperty('_confname');
            $refProperty->setAccessible(true);
            $refProperty->setValue($magmiConfig, $configPath);
        }
    }

    /**
     * Expects array of items to be processed by Magmi.
     * 
     * @param array $items
     */
    protected function import($items, $isUpdateMode = false) {
        $mode = $isUpdateMode ? 'update' : $this->magmiMode;
        
        Utils::log("Executing Magmi import in '%s' mode...", $mode);

        // set the right config pointer
        $this->_setConfigProperty();

        // we start magmi session to load settings
        if (count($items)) {
            
            try {

                $this->magmi->beginImportSession($this->magmiProfile, $mode, new MagmiLogger());

                // reversed due to import simple first than configurables
                $magmiItems = array_reverse($items);

                // reorder first
                //usort($items, array($this, "cmp"));
                //$magmiItems = $items;
                
                // import itself
                foreach ($magmiItems as $item) {
                    Utils::log(\Logger\ILogger::DEBUG, "\n----------\nMagmi Item\n----------" . $this->_itemToString($item));

                    $this->magmi->ingest($item);
                }

                $this->magmi->endImportSession();

                Utils::log("Magmi import in '%s' mode finished (see log above for details).", $mode);
            } catch (\Exception $e) {
                Utils::log("Error during Magmi import process: " . $e->getMessage());
                throw $e;
            }
        } else {
            Utils::log("No items passed in Magmi import.");
        }
    }

    /**
     * Converts Magmi Item to string for log.
     * 
     * @param array $item
     * @return string
     */
    private function _itemToString($item) {
        $str = '';

        foreach ($item as $key => $field) {
            $str .= "\n" . $key . ": " . $field;
        }

        return $str;
    }

    public static function cmp($a, $b) {
        return $b['type'] == 'simple';
    }
}

/**
 * Special log wrapper used for handling Magmi process notices.
 * 
 */
class MagmiLogger {

    public function log($msg) {
        \Gateway\Utils::log(\Logger\ILogger::DEBUG, $msg);
    }

}
