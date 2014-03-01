<?php

namespace Gateway\Handler\Shop\Rakuten\API;

use Gateway\IConnection, 
	Rakuten\Rakuten, 
	Rakuten\Configuration;
	
require_once(WWW_DIR . "/../libs/Rakuten/Rakuten.php");

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
    /*
    public function getClient() {
        if (!$this->client) {
        	$domain = $this->options->get('domain');
            $key = $this->options->get('key');
            //$password = $this->options->get('password');
            $options = array();

            //$this->client = new \Gateway\Handler\Shop\Magento\SOAP($domain, $user, $password, $options);
			
            $this->client = new Rakuten($key, $domain);
			var_dump($this->client);
			
			die;
            //($domain, $user, $password, $options);
        }

        return $this->client;
    }*/
}

