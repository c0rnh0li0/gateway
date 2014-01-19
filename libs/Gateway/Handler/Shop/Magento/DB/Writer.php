<?php

namespace Gateway\Handler\Shop\Magento\DB;

use Gateway\IConnection;

/**
 * An adapter for writing DS to be passed to Magento via direct DB access.
 *
 * @author Lukas Bruha
 */
class Writer extends \Gateway\Handler\Writer {

    protected $nodeType = IConnection::NODE_SHOP;
    
    /**
     * DB connection.
     * 
     * @var \Gateway\Handler\Shop\Magento\SOAP
     */    
    protected $db = null;
    
    /**
     * Expected options for handler.
     * 
     * @var array
     */    
    protected $expectedOptions = array('domain', 'host', 'dbname', 'user', 'password');

    /**
     * Returns DB connection.
     * 
     * @return \Nette\Database\Connection
     */
    public function getDb() {
        if (!$this->db) {
            $host = $this->options->get('host');
            $dbname = $this->options->get('dbname');
            $user = $this->options->get('user');
            $password = $this->options->get('password');

            $db = \Nette\Environment::getContext()->parameters['database'];
            $dsn = sprintf("%s:host=%s;dbname=%s", $db['driver'], $host, $dbname);
            $this->db = new \Nette\Database\Connection($dsn, $user, $password);
        }

        return $this->db;
    }

}

