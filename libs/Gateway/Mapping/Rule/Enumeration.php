<?php

namespace Gateway\Mapping\Rule;

use Gateway\Mapping\Rule;

/**
 * Enumeration mapping rule used for eg. statuses mapping.
 *
 * @author Lukas Bruha
 */
class Enumeration extends Rule {
   
    protected $type = self::TYPE_ENUMERATION;
    
}
