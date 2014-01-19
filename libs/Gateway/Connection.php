<?php

namespace Gateway;

use Gateway\IConnection;

/**
 * Connection entity.
 *
 * @author Lukas Bruha
 */
class Connection extends \Nette\Object implements IConnection {
    
    /**
     * Connection name
     * 
     * @var string
     */   
    protected $name;
    
    /**
     * Connection's handlers
     * 
     * @var array
     */       
    protected $handlers;
    
    /**
     * Required mappings (according to type)
     * 
     * @var array
     */
    protected $mapping;

    /**
     * Inits connection handlers matrix.
     * 
     * @param string $name
     */
    public function __construct($name) {
        $this->setName($name);
        $this->initHandlerMatrix();
    }

    /**
     * Inits handlers structure.
     * 
     */
    protected function initHandlerMatrix() {
        $this->handlers = array();
        $this->handlers[self::NODE_ERP] = array();
        $this->handlers[self::NODE_SHOP] = array();

        $this->handlers[self::NODE_ERP][self::STREAM_READER] = array();
        $this->handlers[self::NODE_ERP][self::STREAM_WRITER] = array();
        $this->handlers[self::NODE_SHOP][self::STREAM_READER] = array();
        $this->handlers[self::NODE_SHOP][self::STREAM_WRITER] = array();
    }
    
    /**
     * Applies product attribute mapping according to defined mapping table.
     * 
     * TODO implement reversed mapping OR specify reversed rules in action
     * 
     * @param \Gateway\Mapping\IMappable $object Object allowing mapping
     * @param string $scope 
     * @param bool $isReversed
     */
    public function applyAttributeMapping(\Gateway\Mapping\IMappable $object, $scope, $isReversed = false) {
        $oldName = $object->mappingName;
        $oldValue = $object->mappingValue;
        $type = $object->mappingType;

        // if we have mapping for specific type and also for old attribute name, 
        // we apply mapping
        if (isset($this->mapping[$type])) {
            // NON REVERSED
            if (!$isReversed) {
                if (isset($this->mapping[$type][$oldName])) {
                    $rule = $this->mapping[$type][$oldName];
                    
                    // attribute mapping must have defined its scope,
                    // eg. product.attributes
                    if ($rule->scope != $scope) {
                        return;
                    }
                    
                    // we change mapping name to new name taken from rule
                    $object->setMappingName($rule->newName);

                    Utils::log(sprintf("Apply mapping rule: '%s'", $rule));

                    // and also if value is requested to be changed, we apply also this
                    if ($rule->newValue && ($oldValue == $rule->oldValue)) {
                        $object->setMappingValue($rule->newValue);
                    }
                }
            } else {
                // TODO implement REVERSED
                Utils::log(sprintf("NOT IMPLEMENTED: Apply of reversed mapping rule: '%s' is not implemented yet.", $rule));
            }
        }        
    }
    
    /**
     * Applies localization/language mapping.
     * 
     * TODO implement reversed mapping
     * 
     * @param \Gateway\Mapping\IMappable $object Object allowing mapping
     * @param bool $isReversed
     */
    public function applyLocalizationMapping(\Gateway\Mapping\ILocalizable $object, $isReversed = false) {
        $lang = $object->lang;
        $type = \Gateway\Mapping\IRule::TYPE_LOCALIZATION;
       
        if (isset($this->mapping[$type])) {
            // NON REVERSED
            if (!$isReversed) {
                if (isset($this->mapping[$type][$lang])) {
                    $rule = $this->mapping[$type][$lang];

					// FIX for 'Categories' handler if admin storeview in localization was added - 'admin' used only in magmi to save default values    N.B. 06.05.2013
					if ($object instanceof \Gateway\DataSource\Entity\Category\LocalizedInfo){ 
												
						Utils::log(sprintf("Debug storeviews:  '%s'",$rule->newName));						
						$langs = explode(",", $rule->newName);
						if (!empty($langs)) {
						$langs = array_map('trim',$langs); //trim white spaces
							if (in_array("admin", $langs)){
								Utils::log(sprintf("Removing admin storeview."));
								$langs = array_diff($langs, array('admin'));							
								$noadmin = implode(",",$langs);
								$rule->setNameMapping($rule->oldName, $noadmin);
								Utils::log(sprintf("Storeviews after admin removed:  '%s'", $rule->newName));
							}else{
								Utils::log(sprintf("No 'admin' storeview in localization found."));							
							}
						}							
					}					
					
                    // we change mapping name to new name taken from rule
                    $object->setLang($rule->newName);

                    Utils::log(sprintf("Apply localization rule: '%s'", $rule));
                }
            } else {
                // TODO implement REVERSED
                foreach ($this->mapping[$type] as $rule) {
                    if ($object->getLang() == $rule->newName) {
                        $object->setLang($rule->oldName);
                        
                        Utils::log(sprintf("Apply reversed localization rule: '%s'", $rule));
                        break;
                    }
                }           
            }
        }
    }
    
    /**
     * Apply property mapping both ways.
     * 
     * @param \Gateway\Mapping\IEnumerable $object
     * @param string $scope
     * @param string $isReversed
     */
    public function applyPropertyMapping(\Gateway\Mapping\IEnumerable $object, $scope = false, $isReversed = false) {
        $value = $object->value;
        $type = \Gateway\Mapping\IRule::TYPE_ENUMERATION;
       
        if (isset($this->mapping[$type])) {
            // NON REVERSED
            if (!$isReversed) {
                if (isset($this->mapping[$type][$value])) {
                    $rule = $this->mapping[$type][$value];

                    if ($scope && ($rule->scope != $scope)) {
                        return;
                    }
                    
                    // we change mapping name to new name taken from rule
                    $object->setValue($rule->newName);

                    Utils::log(sprintf("Apply enumeration rule: '%s'", $rule));
                }
            } else {
                // we cannot search in keys, so we search in values ($rule's newName)
                foreach ($this->mapping[$type] as $rule) {
                    if ($rule->newName == $value) {                    
                        $object->setValue($rule->oldName);
                    
                        Utils::log(sprintf("Apply reversed enumeration rule: '%s'", $rule));
                        break;
                    } 
                }
            }
        }
    }

    /**
     * Regarding handler type puts handler into adapter matrix.
     * 
     * @param \Gateway\IHandler $handler
     * @return \Gateway\Connection
     */
    public function setHandler($handler) {
        if (!$handler->type || !$handler->adapterType || !$handler->nodeType) {
            throw new \Nette\NotSupportedException(sprintf("Handler, node and adapter types must be specified in '%s'.", get_class($handler)));
        }

        $handler->setConnection($this);

        $nodeType = $handler->getNodeType();
        $adapterType = $handler->getAdapterType();
        $handlerType = $handler->getType();

        $this->handlers[$nodeType][$adapterType][$handlerType] = $handler;

        //$adapter = $this->handlers[$nodeType][$adapterType];
        //$adapter->setHandler($handlerType, $handler);

        return $this;
    }

    /**
     * Returns connection's handlers.
     * 
     * @param string $nodeType
     * @param string $adapterType
     * @return array
     */
    public function getHandlers($nodeType, $adapterType) {
        return $this->handlers[$nodeType][$adapterType];
    }

    /**
     * Returns specific connection handler.
     * 
     * @param string $nodeType
     * @param string $adapterType
     * @param string $handlerType
     * @return \Gateway\IHandler
     */
    public function getHandler($nodeType, $adapterType, $handlerType) {
        $handlers = $this->getHandlers($nodeType, $adapterType);

        if (!isset($handlers[$handlerType])) {
            return null;
        }

        return $handlers[$handlerType];
    }

    /**
     * Adds mapping rule to connection.
     * 
     * @param \Gateway\Mapping\IRule $rule
     * @return \Gateway\IConnection
     */
    public function addMappingRule(\Gateway\Mapping\IRule $rule) {
        if (!isset($this->mapping[$rule->type])) {
            $this->mapping[$rule->type] = array();
        }

        $this->mapping[$rule->type][$rule->oldName] = $rule;

        return $this;
    }

    /**
     * Returns mapping matrix.
     * 
     * @return array
     */
    public function getMapping() {
        return $this->mapping;
    }

    /**
     * Returns' connection name.
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets connection name.
     * 
     * @param string $name
     * @return \Gateway\IConnection
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }
    
    /**
     * To string conversion.
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
    
    
}
    