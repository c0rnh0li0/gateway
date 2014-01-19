<?php

namespace Gateway\Handler\Shop\Rakuten\API;

use Rakuten\Engine\Rakuten;


/**
 * Overriden due to Magmi Config init.
 * 
 * @author Lukas Bruha
 */
class API {

    /**
     * General API instance.
     * 
     * @var \APIClient
     */
    protected $client = null;

    /**
     * Created sessions when logged in via SoapClient.
     * 
     * @var mixed
     */
    protected $session = null;

    /**
     * Client settings and login.
     * 
     * @param string $url
     * @param string $key
	 * @param array $options
     */
    public function __construct() {
        $options = array();

        $this->client = new \Rakuten\Engine\Rakuten();
    }
	
    /**
     * Response dump. Prints on screen.
     * 
     */
    public function dump() {
        print "<pre>\n";
        print "Request :\n" . htmlspecialchars($this->client->__getLastRequest()) . "\n";
        print "Response:\n" . htmlspecialchars($this->client->__getLastResponse()) . "\n";
        print "</pre>";
    }

}