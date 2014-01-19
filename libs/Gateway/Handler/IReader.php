<?php

namespace Gateway\Handler;

/**
 * Handler reader interface.
 * 
 * @author Lukas Bruha
 */
interface IReader {
    
    /**
     * Returns reader's input.
     * 
     * @return string
     */
    public function getInput();
    
    /**
     * Sets reader's input.
     * 
     * @param mixed
     */            
    public function setInput($input);
    
    /**
     * Validates readers input
     * 
     * @param mixed
     */
    public function validate($input);
    
}
