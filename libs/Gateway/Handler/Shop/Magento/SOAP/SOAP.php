<?php

namespace Gateway\Handler\Shop\Magento;

ini_set("soap.wsdl_cache_enabled", "0");

/**
 * Overriden due to Magmi Config init.
 * 
 * @author Lukas Bruha
 */
class SOAP {

    /**
     * General SoapClient instance.
     * 
     * @var \SoapClient
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
     * @param string $user
     * @param string $password
     * @param array $options 
     */
    public function __construct($url, $user, $pass, array $options = array()) {
        $options = array();

        ini_set('default_socket_timeout', 600);

        $options = array(
            'soap_version' => SOAP_1_2,
            'exceptions' => 1,
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'connection_timeout' => 86400,
            'user_agent' => ''
        );

        // Magento side link
        $url .= "/index.php/api/soap?wsdl";

        $this->client = new \SoapClient($url, $options);
        $this->session = $this->client->login($user, $pass);
    }

    /**
     * SoapClient call wrapper.
     * 
     * @param string $method
     * @param array $args 
     * @return string
     */
    public function call($method, $args = array()) {
        return $this->client->call($this->session, $method, $args);
    }

    /**
     * SoapClient multicall wrapper.
     * 
     * @param array $calls
     * @return string
     */
    public function multiCall(array $calls) {
        return $this->client->multiCall($this->session, $calls);
    }

    /**
     * Returns last SoapClient response.
     * 
     * @return string
     */
    public function getLastResponse() {
        return $this->client->__getLastResponse();
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