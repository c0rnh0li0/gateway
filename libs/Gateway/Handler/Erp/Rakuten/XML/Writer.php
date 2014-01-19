<?php

namespace Gateway\Handler\Erp\Rakuten\XML;

use Gateway\IConnection;

/**
 * An adapter for writing XML to be passed to Rakuten.
 *
 * @author Darko Krstev
 */
class Writer extends \Gateway\Handler\Writer {

    protected $nodeType = IConnection::NODE_ERP;
    
}

