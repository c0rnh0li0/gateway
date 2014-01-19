<?php

namespace Gateway\Handler\Shop\Rakuten\API;

use Gateway\IConnection, 
	Rakuten\Engine\Rakuten, 
	Rakuten\Configuration\Rakuten_Config;
	
require_once(WWW_DIR . "/../libs/Rakuten/Engine/Rakuten.php");
//require_once(WWW_DIR . "/../libs/Magmi/integration/inc/magmi_datapump.php");
//require_once(WWW_DIR . "/../libs/Magmi/inc/magmi_config.php");

/**
 * An adapter for writing DS to be passed to Magento.
 *
 * @author Darko Krstev
 */
class Writer extends \Gateway\Handler\Writer {

    protected $nodeType = IConnection::NODE_SHOP;
    
    /**
     * SOAP client.
     * 
     * @var \Gateway\Handler\Shop\Rakuten\API\
     */    
    protected $client = null;
    
    /**
     * Expected options for handler.
     * 
     * @var array
     */    
    //protected $expectedOptions = array('domain', 'user', 'password');
	protected $expectedOptions = array();

    /**
     * Returns SOAP client.
     * 
     * @return \Gateway\Handler\Shop\Magento\SOAP
     */
    public function getClient() {
        if (!$this->client) {
            //$domain = $this->options->get('domain');
            //$user = $this->options->get('user');
            //$password = $this->options->get('password');
            $options = array();
			
            $this->client = new Rakuten();
            //($domain, $user, $password, $options);
        }

        return $this->client;
    }

}

