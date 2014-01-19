<?php
namespace Gateway\Handler;

/**
 * Reader abstract class.
 * 
 * @author Lukas Bruha
 */

abstract class Writer extends \Gateway\Handler implements IWriter {
       
    /**
     * Adapter type.
     * 
     * @var string
     */        
    protected $adapterType = \Gateway\IConnection::STREAM_WRITER;
    
    /**
     * Datasource itself.
     * 
     * @var \Gateway\IDataSource
     */        
    private $dataSource = null;

    /**
     * Sets datasource.
     * 
     * @param \Gateway\IDataSource
     */        
    public function setDataSource($dataSource) {
        $this->dataSource = $dataSource;
    }

    /**
     * Returns datasource.
     * 
     * @return \Gateway\IDataSource
     */        
    public function getDataSource() {
        return $this->dataSource;
    }
}