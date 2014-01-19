<?php
namespace Gateway\Handler;

use Gateway\Handler\IReader;

/**
 * Reader abstract class.
 * 
 * @author Lukas Bruha
 */

abstract class Reader extends \Gateway\Handler implements IReader {

    /**
     * Datasource.
     * 
     * @var \Gateway\IDataSource
     */    
    protected $dataSource = null;
    
    /**
     * Adapter type.
     * 
     * @return string
     */        
    protected $adapterType = \Gateway\IConnection::STREAM_READER;
    
    /**
     * Returns input of reader.
     * 
     * @return mixed
     */
    public function getInput() {
        return $this->input;
    }
    
    /**
     * Sets reader's input.
     * 
     * FIXME allows only Reader\Input wrapper object to be passed 
     * 
     * @param type $input
     * @throws \Nette\IOException
     */
    public function setInput($input) {
        $res = $this->validate($input);
        
        if (!$res) {
            throw new \Nette\IOException(sprintf("Input is not valid for '%s' reader.", get_class($this)));
        }
        
        // in $res can be changed input
        $this->input = $res === true ? $input : $res;
        
        return $this;
    }
    
    /**
     * Validates reader's input.
     * 
     * @param mixed $input
     * @return boolean
     */
    public function validate($input) {
        return true;
    }

}