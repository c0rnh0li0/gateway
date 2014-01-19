<?php

namespace Gateway\DataSource;

/**
 * General entity interface accepted by Gateway DataSource.
 * 
 * @author Lukas Bruha
 */
interface IEntity {
    
    /**
     * Returns entity key attribute used as unique key in data array.
     * 
     */
    public function getKey();
    
}
