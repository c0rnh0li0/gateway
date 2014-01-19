<?php

namespace Gateway\Handler;

/**
 * Handler writer interface.
 * 
 * @author Lukas Bruha
 */
interface IWriter {

    /**
     * Sets prepared datasource to writer.
     * 
     * @param \Gateway\IDataSource
     */        
    public function setDataSource($dataSource);

    /**
     * Returns datasource.
     * 
     * @param \Gateway\IDataSource
     */             
    public function getDataSource();
    
}
