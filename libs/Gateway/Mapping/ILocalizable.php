<?php

namespace Gateway\Mapping;

/**
 * Special type of mappable interface must have every entity that allows mapping
 * of languages.
 * 
 * @author Lukas Bruha
 */
interface ILocalizable {
 
    /**
     * Returns language code.
     * 
     * @return mixed
     */        
    public function getLang();

    /**
     * Sets language code.
     * 
     */        
    public function setLang($lang);
    
}
