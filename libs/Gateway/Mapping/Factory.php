<?php

namespace Gateway\Mapping;

/**
 * Factory for mapping rules.
 * 
 * @author Lukas Bruha
 */
class Factory {
    
    /**
     * Factory method to create a specific type of supported mapping class.
     * 
     * @param string $type
     * @param array $names
     * @param array $values
     * @param string $handlerType
     * @param string $scope
     * @return \Gateway\Mapping\Rule\Attribute
     */
    public static function create($type, $names, $values = array(), $handlerType = null, $scope = null) {        
        switch ($type) {
            case \Gateway\Mapping\IRule::TYPE_LOCALIZATION:
                $rule = new \Gateway\Mapping\Rule\Localization($names, $values, $handlerType, $scope);

                break;
            case \Gateway\Mapping\IRule::TYPE_ENUMERATION:
                $rule = new \Gateway\Mapping\Rule\Enumeration($names, $values, $handlerType, $scope);

                break;
            case \Gateway\Mapping\IRule::TYPE_ATTRIBUTE:
                $rule = new \Gateway\Mapping\Rule\Attribute($names, $values, $handlerType, $scope);
                break;
            default:
                throw new \Nette\InvalidArgumentException(sprintf("Unsupported mapping type requested: '%s'.", $type));
                break;
        }

        return $rule;
    }
    
}
