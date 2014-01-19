<?php

namespace Gateway\Handler\Erp\Etron\XML;

use Gateway\IConnection;

/**
 * An adapter for writing XML to be passed to ETRON.
 *
 * @author Lukas Bruha
 */
class Writer extends \Gateway\Handler\Writer {

    protected $nodeType = IConnection::NODE_ERP;
    
}

