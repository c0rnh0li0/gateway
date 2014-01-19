<?php

use Nette\Diagnostics\Debugger,
    Gateway\Utils,
    Gateway\Handler,       
    Gateway\Handler\Options,
    Gateway\Connections,
    Gateway\Connection,
    Gateway\IConnection;

/**
 * Base presenter for the whole application incl. modules.
 * 
 * @author Lukas Bruha
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {

    /**
     * Logger service instance.
     * @var type 
     */
    protected $logger = null;
    
    /**
     * Connections list.
     * Key is the name of connection.
     * 
     * @var array
     */
    protected $connections = array();

    public function startup() {
        parent::startup();
        
        $this->logger = $this->getService('FileLogger');
        //$this->logger->logMessage(sprintf("*** REQUEST '%s' CALLED  ***", $this->getAction(true)));
        
        $this->initConnections();
    }

    /**
     * Loads available connections and all included data.
     * 
     * @return array
     */
    protected function initConnections() {
        if (!count($this->connections)) {

            $connections = Connections::getInstance();

            foreach ($this->getService('database')->table('gw_connection') as $conn) {
                $connId = $conn->id;
                $connHandler = $this->getService('database')
                        ->table('gw_adapter')
                        ->select('gw_handler.*, gw_adapter.settings')
                        ->where(array('gw_connection_id' => $connId));

                // CONNECTION
                $connection = new \Gateway\Connection($conn->name);
                $this->getService('FileLogger')->logMessage(\Logger\ILogger::DEBUG, sprintf(">>> Connection: '%s' <<<", $conn->name));   

                // HANDLERS
                //foreach ($connHandler as $adapterHandler) {
                foreach ($connHandler as $handler) {
                    // handler settings
                    $options = new Options($handler->settings);

                    // handler itself
                    $handlerClassName = '\\' . $handler->class;
                    $handlerClass = new $handlerClassName($options);
                    
                    // put handler to connection
                    $connection->setHandler($handlerClass);
                    
                    $this->getService('FileLogger')->logMessage(\Logger\ILogger::DEBUG, sprintf("Adding handler: '%s'", $handlerClassName));                    
                }

                // MAPPING RULES
                $condition = array('gw_connection_id' => $connId, 'is_enabled' => 1);
                $mappingRules = $this->getService('database')
                                    ->table('gw_mapping_rule')
                                    ->select('gw_mapping_rule.*, 
                                                gw_handler_type.id AS handler_type_id,
                                                gw_handler_type.name AS handler_type, 
                                                gw_mapping_scope.name AS mapping_scope_id,
                                                gw_mapping_scope.name AS mapping_scope')
                                    ->where($condition);

                foreach ($mappingRules as $mappingRule) {
                    $names = array($mappingRule->old_name => $mappingRule->new_name);
                    $values = array();

                    $handlerType = $mappingRule->handler_type;
                    $scope = $mappingRule->mapping_scope;
                    
                    if ($mappingRule->old_value && $mappingRule->new_value) {
                        $values[$mappingRule->old_value] = $mappingRule->new_value;
                    }

                    $rule = \Gateway\Mapping\Factory::create($mappingRule->type, $names, $values, $handlerType, $scope);
                    $connection->addMappingRule($rule);

                    $this->getService('FileLogger')->logMessage(\Logger\ILogger::DEBUG, sprintf("Adding mapping rule: '%s'", $rule));                    
                }

                $connections->add($connection);
                $this->connections = $connections;
            }

            $this->connections = $connections;
        }
        
        return $this->connections;
    }

}