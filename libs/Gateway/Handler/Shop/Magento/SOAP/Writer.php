<?php

namespace Gateway\Handler\Shop\Magento\SOAP;

use Gateway\IConnection;

/**
 * An adapter for writing DS to be passed to Magento.
 *
 * @author Lukas Bruha
 */
class Writer extends \Gateway\Handler\Writer {

    protected $nodeType = IConnection::NODE_SHOP;
    
    /**
     * SOAP client.
     * 
     * @var \Gateway\Handler\Shop\Magento\SOAP
     */    
    protected $client = null;
    
    /**
     * Expected options for handler.
     * 
     * @var array
     */    
    protected $expectedOptions = array('domain', 'user', 'password');

    /**
     * Returns SOAP client.
     * 
     * @return \Gateway\Handler\Shop\Magento\SOAP
     */
    public function getClient() {
        if (!$this->client) {
            $domain = $this->options->get('domain');
            $user = $this->options->get('user');
            $password = $this->options->get('password');
            $options = array();

            $this->client = new \Gateway\Handler\Shop\Magento\SOAP($domain, $user, $password, $options);
        }

        return $this->client;
    }

}

