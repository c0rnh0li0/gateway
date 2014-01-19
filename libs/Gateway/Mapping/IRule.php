<?php

namespace Gateway\Mapping;

/**
 * Mapping rule interface.
 * 
 * @author Lukas Bruha
 */
interface IRule {
    
    const TYPE_LOCALIZATION = 'localization';
    const TYPE_ATTRIBUTE = 'attribute';
    const TYPE_ENUMERATION = 'enumeration';
    
}
