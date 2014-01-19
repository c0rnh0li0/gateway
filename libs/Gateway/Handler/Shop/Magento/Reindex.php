<?php

namespace Gateway\Handler\Shop\Magento;

use Gateway\Utils;

/**
 * Magento specific tools.
 * 
 * @author Lukas Bruha
 */
class Reindex {

    /**
     * Magento host.
     * 
     * @var string
     */    
    protected $url;
    
    /**
     * Magento side script.
     * 
     * @var string
     */        
    protected $script = 'indexer_control.php';
    
    /**
     * Indexer script parameters.
     * 
     * @var array
     */        
    protected $actions = array(
        'init' => '?action=manual',
        'execute' => '?action=reindex',
        'finish' => '?action=automatic',
    );

    /**
     * Sets Magento host.
     * 
     * @param string $url
     */        
    public function __construct($url) {
        $this->url = $url;
    }

    /**
     * Performs init script to set mode to manual.
     * 
     * @return int
     */
    public function init() {
        $url = $this->getUrl() . $this->actions['init'];

        Utils::log(sprintf("REINDEX: Initializing reindex on '%s'...", $url));
        $res = $this->curl($url);
        
        if ($res == 404) {
            Utils::log(\Logger\ILogger::ERROR, sprintf("REINDEX: Cannot set mode to 'manual'. Remote script '%s' does not exists.", $url));
        } else {
            Utils::log("REINDEX: Initialized. Mode set to 'manual'.");
        }
        
        return $res;
    }

    /**
     * Performs execute script to reindex all.
     * 
     * @return int
     */
    public function execute() {
        $url = $this->getUrl() . $this->actions['execute'];

        Utils::log(sprintf("Reindexing all on '%s'...", $url));
        $res = $this->curl($url);
        
        if ($res == 404) {
            Utils::log(\Logger\ILogger::ERROR, sprintf("REINDEX: Cannot 'reindex all'. Remote script '%s' does not exists.", $url));
        } else {
            Utils::log("REINDEX: Reindexed all.");
        }
        
        return $res;
    }
    
    /**
     * Performs finish script to re-set mode to automatic.
     * 
     * @return int
     */
    public function finish() {
        $url = $this->getUrl() . $this->actions['finish'];

        Utils::log(sprintf("Setting mode to 'automatic' on '%s'...", $url));
        $res = $this->curl($url);
        
        if ($res == 404) {
            Utils::log(\Logger\ILogger::ERROR, sprintf("REINDEX: Cannot set mode to 'automatic'. Remote script '%s' does not exists.", $url));
        } else {
            Utils::log("REINDEX: Finished. Mode set to 'automatic'.");
        }
        
        return $res;
    }

    /**
     * Performs remote calling.
     * 
     * @return int
     */        
    protected function curl($url) {
        // init
        $ch = curl_init($url);

        // set options
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // perform post
        $res = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

        curl_close($ch);
        
        
        return $httpCode;
    }

    /**
     * Concats Magento host, script and parameters.
     * 
     * @return string
     */        
    protected function getUrl() {
        $url = \Nette\Utils\Strings::endsWith($this->url, "/") ? $this->url : $this->url . "/"; 
        $script = \Nette\Utils\Strings::startsWith($this->script, "/") ? $this->script : "/" . $this->script; 
        
        return $url . $script;
    }

}

