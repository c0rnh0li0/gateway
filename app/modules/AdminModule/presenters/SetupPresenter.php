<?php

namespace AdminModule;

use Nette\Diagnostics\Debugger,
    Yourface\Application\UI\Form,
    Nette\Utils\Html;

/**
 * Connection specific setup (attributes, handlers etc.).
 *  
 * @author Lukas Bruha
 * 
 */
class SetupPresenter extends ConnectionPresenter {

    /**
     * Loaded connection.
     * 
     * @var \Nette\Database\Statement\Row
     */    
    protected $connection = null;
    
    /**
     * Loaded connection handlers (adapters).
     * 
     * @var \Nette\Database\Statement\Selection
     */    
    protected $adapters;

    /**
     * Loaded general handlers (adapters).
     * 
     * @var \Nette\Database\Statement\Selection
     */   
    protected $handlers;
    
    /**
     * Type of mapping (localization, attributes etc.)
     * 
     * @var \Nette\Database\Statement\Selection
     */       
    protected $mappingRulesType = null;

    /** @persistent */
    public $id = 0;

    /**
     * Connection ID required.
     * 
     */
    public function startup() {
        parent::startup();

        if (!$this->id) {
            $this->flashMessage("Missing parameter 'id'.", 'error');
            $this->redirect('Connection:default');
        }

        $this->connection = $this->getService('connections')->get($this->id);

        if (!$this->connection) {
            $this->flashMessage(sprintf("Connection having ID of '%s' does not exist.", $this->id), 'error');
            $this->redirect('Connection:default');
        }

        $this->adapters = $this->getService('adapters');
    }

    /**
     * Adds connection to every template.
     * 
     */
    public function beforeRender() {
        parent::beforeRender();

        $this->template->connection = $this->connection;
    }

    /**
     * Attributes mapping.
     * 
     */    
    public function actionAttributes() {
        $this->mappingRulesType = \Gateway\Mapping\IRule::TYPE_ATTRIBUTE;
    }

    /**
     * Localization mapping.
     * 
     */    
    public function actionLocalization() {
        $this->mappingRulesType = \Gateway\Mapping\IRule::TYPE_LOCALIZATION;
    }

    /**
     * Other mapping (products attributes etc.).
     * 
     */    
    public function actionEnumeration() {
        $this->mappingRulesType = \Gateway\Mapping\IRule::TYPE_ENUMERATION;
    }
    
    /**
     * Load edit handler form.
     * 
     */    
    public function actionEditHandler($readerId, $writerId) {
        $where = array('gw_connection_id' => $this->id, 'gw_handler_id' => array($readerId, $writerId));
        $adapters = $this->getService('database')
                            ->table('gw_adapter')
                            ->select('gw_adapter.*, gw_handler.id AS handler_id, gw_handler.settings_mask, gw_handler.node, gw_handler.type, gw_handler.stream, gw_handler.class, gw_handler.description')
                            ->where($where);
        
        $form = $this->getComponent('editHandlerForm');
        
        foreach ($adapters as $adapter) {
            /*$type = ($adapter->node == \Gateway\IConnection::NODE_ERP && $adapter->stream == \Gateway\IConnection::STREAM_READER)
                    || ($adapter->node == \Gateway\IConnection::NODE_SHOP && $adapter->stream == \Gateway\IConnection::STREAM_WRITER);
            $text = "[" . ($type ? 'erp -> shop' : 'shop -> erp') . "] " . $adapter->class;
            */
            
            $text = $adapter->description;
            $form[$adapter->stream]->setValue($text)->setOption('description', $adapter->class);
            $form[$adapter->stream . '_settings']->setDefaultValue($adapter->settings)->setOption('description', $adapter->settings_mask);
            
            $form[$adapter->stream . '_id']->setValue($adapter->handler_id);
        }
    }

    /**
     * Connection handlers.
     * 
     * @return \AdminModule\Component\ConnectionHandlersGrid
     */
    public function createComponentConnectionHandlersGrid() {
        // FIXME rewrite to fluent
        $adapters = $this->getService('database')
                            ->query("SELECT
                                    CONCAT(`reader`.`node`, ' > ', `writer`.`node`) AS `direction`,

                                    `reader`.`type` AS `type`,
                                    `adapter_reader`.`gw_connection_id` AS `id`,
                                    
                                    `adapter_reader`.`gw_handler_id` AS `adapter_reader_id`,
                                    `adapter_writer`.`gw_handler_id` AS `adapter_writer_id`,

                                    `reader`.`description` AS `reader_description`,
                                    `writer`.`description` AS `writer_description`,

                                    `reader`.`class` AS `reader_class`,
                                    `writer`.`class` AS `writer_class`,
                                    `reader`.`stream` AS `reader_stream`,
                                    `writer`.`stream` AS `writer_stream`,

                                    `reader`.`settings_mask` AS `reader_settings_mask`,
                                    `adapter_reader`.`settings` AS `adapter_reader_settings`,

                                    `writer`.`settings_mask` AS `writer_settings_mask`,
                                    `adapter_writer`.`settings` AS `adapter_writer_settings`

                                    FROM  
                                    `gw_adapter` AS `adapter_reader`
                                    INNER JOIN 
                                    `gw_handler` as `reader` 
                                    ON `adapter_reader`.`gw_handler_id` = `reader`.`id` 
                                    AND `reader`.`stream` = 'reader'

                                    INNER JOIN 
                                    `gw_adapter` as `adapter_writer`
                                    INNER JOIN 
                                    `gw_handler` as `writer` 
                                    ON `adapter_writer`.`gw_handler_id` = `writer`.`id` 
                                    AND `writer`.`stream` = 'writer'

                                    WHERE (`adapter_reader`.`gw_connection_id` = ?) 
                                    AND (`adapter_writer`.`gw_connection_id` = ?) 
                                    AND (`adapter_reader`.`gw_connection_id`= `adapter_writer`.`gw_connection_id`)
                                    AND (`writer`.`node` != `reader`.`node`)
                                    AND `writer`.`type` = `reader`.`type`
                                    ORDER BY `reader`.`type` DESC, `reader`.`node` ASC", $this->id, $this->id);
        
        $grid = new \AdminModule\Component\ConnectionHandlersGrid($adapters);

        return $grid;
    }

    
    /**
     * Adding new connection handlers.
     * 
     * @return \Yourface\Application\UI\Form
     */
    public function createComponentEditHandlerForm() {
        $form = new Form($this, 'editHandlerForm');
        
        $form->addHidden('reader_id');
        $form->addHidden('writer_id');
        $form->addText('reader', "Reader")->setDisabled(true);
        $form->addText('writer', "Writer")->setDisabled(true);
        $form->addText('reader_settings', "with settings");
        $form->addText('writer_settings', "with settings");
        $form->addSubmit('save', 'Save')->setAttribute('class', 'default');
        $form->addSubmit('cancel', 'Cancel')->setValidationScope(NULL);
        $form->onSuccess[] = callback($this, 'handleEditHandlerFormSuccess');
        $form->addProtection('Please submit this form again (security token has expired).');        
    }
    
    public function handleEditHandlerFormSuccess(Form $form) {
        if (isset($form['cancel']) && $form['cancel']->isSubmittedBy()) {
            $this->redirect('default');
        } 
        
        $vals = $form->getValues();
        $handlers = array(
                        $vals['reader_id'] => $vals['reader_settings'], 
                        $vals['writer_id'] => $vals['writer_settings']
                    );        
        
        try {
            foreach ($handlers as $id => $settings) {
                $where = array('gw_handler_id' => $id, 'gw_connection_id' => $this->id);
                
                $this->getService('database')
                        ->table('gw_adapter')
                        ->where($where)
                        ->update(array('settings' => $settings));
            }
            
            $this->flashMessage("Handler's settings updated successfully.", 'success');
            $this->redirect('default');
        } catch(\PDOException $e) {
            $this->flashMessage("Error raised during handler's settings update: " . $e->getMessage(), 'error');
            
        }
   }
    
    //////////////////////////////
    // CONNECTION HANDLERS FORM //
    //////////////////////////////
    /**
     * Adding new connection handlers.
     * 
     * @return \Yourface\Application\UI\Form
     */
    public function createComponentConnectionHandlersForm() {
        $form = new Form($this, 'connectionHandlersForm');
 
        $types = $this->getService('database')
                            ->table('gw_handler_type')
                            ->order('name ASC')
                            ->fetchPairs('name', 'name');

        $readers = array();
        $writers = array();
        $types = array();
        $handlers = $this->getService('database')
                            ->table('gw_handler');
        
        foreach($handlers as $handler) {
            $types[$handler->type] = $handler->type;
            
            if ($handler->stream == \Gateway\IConnection::STREAM_READER) {
                $readers[$handler->id] = $handler->description;
            } elseif ($handler->stream == \Gateway\IConnection::STREAM_WRITER) {                
                $writers[$handler->id] = $handler->description;
            }
        }
        
        $form->addSelect('type', "To handle", $types)
                ->setPrompt('- select -')
                ->setRequired("'To handle' is required.");
        $form->addSelect('reader', "Read from", $readers)
                ->setPrompt('- select -')
                ->setRequired("'Read from' is required.");
        $form->addText('reader_settings', "with settings");
        $form->addSelect('writer', "Write to", $writers)
                ->setPrompt('- select -')
                ->setRequired("'Read from' is required.");
        $form->addText('writer_settings', "with settings");
        $form->addSubmit('save', 'Save')->setAttribute('class', 'default');
        $form->onSuccess[] = callback($this, 'handleConnectionHandlersFormSubmitted');
        $form->onValidate[] = callback($this, 'handleConnectionHandlersFormValidate');
        
        $form->addProtection('Please submit this form again (security token has expired).');
        
        // only non submitted are disabled
        if (!$form->isSubmitted()) {
            $form['reader']->setDisabled(true);
            $form['reader_settings']->setDisabled(true);
            $form['writer']->setDisabled(true);
            $form['writer_settings']->setDisabled(true);
        }

        return $form;
    }
    
    /**
     * Validates handler settings after submit and adds errors if any.
     * 
     */
    public function handleConnectionHandlersFormValidate($form) {
        $values = $form->getValues();
        $handlersSettings = array(
                                $values['reader'] => $values['reader_settings'], 
                                $values['writer'] => $values['writer_settings']
                            );
        
        // check what needs settings by checking NULL values 
        $handlers = $this->getService('database')
                        ->table('gw_handler')
                        ->where(array('id' => array_keys($handlersSettings)));
        
        foreach ($handlers as $handler) {
            $mask = $handler->settings_mask;
            
            // settings required?
            if ($mask) {
                $formHandlerSettings = $handlersSettings[$handler->id];
                
                // first check if it was filled
                if (!$formHandlerSettings) {
                    $form->addError(sprintf("Settings for '%s' required. Expected format: '%s'.", $handler->description, $mask));
                } else { // second we check if it was filled correctly
                    try {
                        //dump($formHandlerSettings);
                        $options = new \Gateway\Handler\Options($formHandlerSettings);
                        $class = "\\" . $handler->class;
                        
                        echo $handler->class;
                        // create an instance of handler and validate
                        $instance = new $class($options);
                    } catch (\Nette\InvalidArgumentException $e) {
                        // settings set but not correctly
                        $form->addError(sprintf("Invalid options for '%s'. Expected format: '%s'.", $handler->description, $mask));
                    }
                }
            }            
        }
        
    }
    
    /**
     * Saving of the new connection handlers.
     * 
     * @param \Yourface\Application\UI\Form $form
     */
    public function handleConnectionHandlersFormSubmitted(Form $form) {
        $values = $form->getValues();
        
        // general prescription
        $data = array(
            'gw_connection_id' => $this->getParam('id'),
            'gw_handler_id' => array(),
            'settings' => array(),
        );
        
        // reader data mapping
        $readerData =  $data;
        $readerData['gw_handler_id'] = (int) $values['reader'];
        $readerData['settings'] = $values['reader_settings'];

        // writer data mapping
        $writerData =  $data;
        $writerData['gw_handler_id'] = (int) $values['writer'];
        $writerData['settings'] = $values['writer_settings'];
   
        try {
            // multi insert 
            $this->getService('database')
                    ->exec('INSERT INTO gw_adapter', array($readerData, $writerData ));
            
            $this->flashMessage("Handlers have been added to connection.", 'success');
        } catch (\PDOException $e) {
            // 23000 means integrity constraints
            if ($e->getCode() == 23000) {
                $this->flashMessage("Such handlers already defined.", 'error');
            } else {
                $this->flashMessage("There was an error during handlers adding: " . $e->getMessage(), 'error');
            }
        }

        $this->redirect('this');        
    }
    
    /**
     * Loading readers according to type.
     * 
     * @param string $type
     */
    public function handleConnectionHandlersFormReaders($type) {
        $form = $this->getComponent("connectionHandlersForm");

        // data preparing
        $readers = array();
        $disabled = true;

        // get reader for this type
        if ($type) {
            $readers = $this->getService('database')
                            ->table('gw_handler')
                            ->where(array(
                                'type' => $type, 
                                'stream' => \Gateway\IConnection::STREAM_READER
                            ))->fetchPairs('id', 'description');
        
            $form['type']->setValue($type);
        } else {
            $form['reader']->setDisabled(true);
        }

        if (count($readers)) {
            $form['reader']->setItems($readers)
                            ->setDisabled(false);
            
            $form['reader_settings']
                        ->setValue('')
                        ->setDisabled(false);
        }
        
        // invalidate snippet
        $this->invalidateControl("connectionHandlersFormReader");
        $this->invalidateControl("connectionHandlersFormWriter");
    }
    
    /**
     * Loading connection writers according to given type and selected reader.
     * 
     * @param string $type
     * @param int $reader
     */
    public function handleConnectionHandlersFormWriters($type, $reader) {
        $form = $this->getComponent("connectionHandlersForm");
        
        // data preparing
        $writers = array();
        $readers = array();
        
        if ($type && $reader) {
            $allReaders = $this->getService('database')
                     ->table('gw_handler')
                     ->where(array(
                         'type' => $type, 
                         'stream' => \Gateway\IConnection::STREAM_READER
                     ));

            $node = false;

            foreach ($allReaders as $r) {
                if ($r->id == $reader) {
                    $node = $r->node;
                }

                $readers[$r->id] = $r->class;
            }

            // get reader for this type
            $writers = $this->getService('database')
                            ->table('gw_handler')
                            ->where(array(
                                'type' => $type,
                                'node != ?' => $node,
                                'stream' => \Gateway\IConnection::STREAM_WRITER)
                             )->fetchPairs('id', 'description');

            $disabled = false;

            $form['type']->setValue($type);
            $form['reader']->setItems($readers)
                            ->setValue($reader)
                            ->setDisabled($disabled);
            $form['writer']->setItems($writers)
                            ->setDisabled($disabled);

            $form['writer_settings']->setValue('')
                            ->setDisabled(false);
        } else {
            $form['writer']->setDisabled(true);
            $form['writer_settings']->setDisabled(true);
        }
        
        // invalidate snippet
        $this->invalidateControl("connectionHandlersFormWriter");
    }

    /**
     * Connection handler form submitting.
     * 
     * @param \Yourface\Application\UI\Form $form
     */
    public function handleConnectionHandlerFormSubmitted(Form $form) {
        $values = $form->getValues();

        $data = array(
            'gw_connection_id' => $this->getParam('id'),
            'gw_handler_id' => $values['class'],
            'settings' => $values['settings'],
        );

        try {
            $this->adapters->insert($data);
            $this->flashMessage("Handler has been added to connection.", 'success');
        } catch (\PDOException $e) {
            // 23000 means integrity constraints
            if ($e->getCode() == 23000) {
                $this->flashMessage("Such handler already defined.", 'error');
            } else {
                $this->flashMessage("There was an error during handler adding: " . $e->getMessage(), 'error');
            }
        }

        $this->redirect('this');
    }

    ///////////////////////
    // MAPPING RULE FORM //
    ///////////////////////
    /**
     * Creates mapping rule form.
     * 
     * @return \Yourface\Application\UI\Form
     */
    public function createComponentMappingRuleForm() {
        $form = new Form;
        $form->addHidden('id');
        $form->addHidden('type')->setValue($this->mappingRulesType);

        // scopes applied only for enumeration
        if ($this->mappingRulesType == \Gateway\Mapping\IRule::TYPE_ENUMERATION) {
            $scopes = $this->getService('database')
                    ->table('gw_mapping_scope')
                    ->where(array('name NOT' => array('product.attributes', '*.lang')))
                    ->fetchPairs('id', 'name');

            $form->addSelect('gw_mapping_scope_id', 'Only for', $scopes)
                    ->setPrompt('- all -');
           
            $form->addText('old_name', "ERP value");
            $form->addText('new_name', "SHOP value");
        } else {
            $form->addText('old_name', "ERP name");
            $form->addText('new_name', "SHOP name");            
        }

        // values
        $form->addText('old_value', "ERP value")
                ->setOption('description', 'Leave empty for apply to all values.');
        $form->addText('new_value', "Shop value")
                ->setOption('description', 'Leave empty for apply to all values.');

        $form['old_value']
                ->addConditionOn($form['new_value'], $form::FILLED)
                ->addRule($form::FILLED, sprintf("Also '%s' element must be specified", $form['old_value']->getLabel()->getText()));

        $form['new_value']
                ->addConditionOn($form['old_value'], $form::FILLED)
                ->addRule($form::FILLED, sprintf("Also '%s' element must be specified", $form['new_value']->getLabel()->getText()));

        $form->addCheckbox('is_enabled', 'Is enabled?')
                ->setDefaultValue(true);

        $form->addSubmit('save', 'Add rule')->setAttribute('class', 'default');
        $form->onSuccess[] = callback($this, 'handleMappingRuleFormSubmitted');

        $form->addProtection('Please submit this form again (security token has expired).');

        return $form;
    }

    /**
     * Handled when passing new rule.
     * 
     * @param \Yourface\Application\UI\Form $form
     */
    public function handleMappingRuleFormSubmitted(Form $form) {
        $values = $form->getValues();

        try {
            unset($values['id']);
            $values['gw_connection_id'] = $this->id;

            // for attribute we set 'products' handler and 'product.attributes' scope
            if ($this->mappingRulesType == \Gateway\Mapping\IRule::TYPE_ATTRIBUTE) {
                $scope = $this->getService('database')
                        ->table('gw_mapping_scope')
                        ->where(array('name' => 'product.attributes'))
                        ->fetch();

                $values['gw_mapping_scope_id'] = $scope->id;
                $values['gw_handler_type_id'] = $scope->gw_handler_type_id;
            } elseif ($this->mappingRulesType == \Gateway\Mapping\IRule::TYPE_LOCALIZATION) {
                $scope = $this->getService('database')
                        ->table('gw_mapping_scope')
                        ->where(array('name' => '*.lang'))
                        ->fetch();
                
                $values['gw_mapping_scope_id'] = $scope->id;
            } else {
                $scope = $this->getService('database')
                        ->table('gw_mapping_scope')
                        ->where(array('id' => $values['gw_mapping_scope_id']))
                        ->fetch();

                $values['gw_handler_type_id'] = $scope->gw_handler_type_id;
            }

            $this->getService('database')
                    ->table('gw_mapping_rule')
                    ->insert($values);

            $this->flashMessage("Mapping rule has been added to connection.", 'success');
        } catch (\PDOException $e) {
            // 23000 means integrity constraints
            if ($e->getCode() == 23000) {
                $this->flashMessage("Such mapping rule already defined.", 'error');
            } else {
                $this->flashMessage("There was an error during mapping rule adding: " . $e->getMessage(), 'error');
            }
        }

        $this->redirect('this');
    }

    ////////////////////////
    // MAPPING RULES GRID //
    ////////////////////////
    /**
     * Mapping rules grid - used for all types of mapping.
     * 
     * @return \AdminModule\Component\LocalizationRulesGrid
     */
    public function createComponentMappingRulesGrid() {
        $rules = $this->getService('database')
                ->table('gw_mapping_rule')
                ->select('gw_mapping_rule.*, gw_handler_type.name AS gw_handler_type_id, gw_mapping_scope.name AS gw_mapping_scope_id')
                ->where(array('gw_connection_id' => $this->id, 'type' => $this->mappingRulesType));

        // get specific data grid
        if ($this->mappingRulesType == \Gateway\Mapping\IRule::TYPE_ATTRIBUTE) {
            $grid = new \AdminModule\Component\MappingRulesGrid($rules);
        } elseif ($this->mappingRulesType == \Gateway\Mapping\IRule::TYPE_LOCALIZATION) {
            $grid = new \AdminModule\Component\LocalizationRulesGrid($rules);
        } elseif ($this->mappingRulesType == \Gateway\Mapping\IRule::TYPE_ENUMERATION) {
            $grid = new \AdminModule\Component\EnumerationRulesGrid($rules);
        } else {

            throw new \Nette\InvalidArgumentException("Unsupported grid for mapping rules.");
        }

        return $grid;
    }

}
