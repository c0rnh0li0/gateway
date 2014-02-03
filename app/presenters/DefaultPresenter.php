<?php

use Nette\Diagnostics\Debugger,
    Gateway\Connection,
    Gateway\Connections,
    Gateway\IConnection,
    Gateway\Utils,
    Nette\Application\Responses\TextResponse,
    Nette\Application\UI\Form,
    Nette\Utils\Html,
    Nette\Diagnostics\Logger,
    Nette\Mail\Message, 
	Rakuten\Engine\Rakuten;

/**
 * Main actions to process ERP SHOP synchronizations.
 * 
 */
class DefaultPresenter extends BasePresenter {

    /**
     * Available inputs for readers.
     * 
     * @var array
     */
    protected $source = array();

    /**
     * Executes connection processing ACCORDING to the schedule(s). 
     * 
     * @param int $scheduleId
     */
    public function actionExecute($scheduleId = 0) {

        $logDir = $this->logger->getLogDir();
    
        // Check if maintenance was started
        if (@file_exists($logDir . '/maintenance-started')){
            $this->presenter->flashMessage("Maintenace was started, all scheduled handlers are stopped!", 'error');
            $this->logger->logMessage("Maintenace was started, all scheduled handlers are STOPPED!");
            return false;
        }    
    
        // SEND E-mail for idle or unsuccessfully executed handlers         
        $bodyText = "";
        $emailSnooze = 86400; // 1 day
        
        $idleSchedules = $this->getService('database')
                        ->table('gw_schedule')
                        ->select('id')
                        ->where(array('status' => 'processing', 'is_cancelled' => 0, 'is_archived' => 0, '?' => new \Nette\Database\SqlLiteral('TIMESTAMPDIFF(MINUTE,executed_at,NOW()) > 120')));
        
        if ($idleSchedules->count() > 0){
            $bodyText .= "Idle Handlers:";            
            $bodyText .= implode(",", $idleSchedules->fetchPairs('id'));
        }
        
        $errorSchedules = $this->getService('database')
                        ->table('gw_schedule')
                        ->select('id')
                        ->where(array('status' => 'finished', 'is_cancelled' => 1, 'is_archived' => 0,'?' => new \Nette\Database\SqlLiteral('inserted_at BETWEEN (NOW() - INTERVAL 30 DAY) AND NOW()')));
                        
        if ($errorSchedules->count() > 0){
            $bodyText .= "\nError Handlers:";
            $bodyText .= implode(",", $errorSchedules->fetchPairs('id'));        
        }
        //$this->logger->logMessage("DEBUG ERRORS: '%s' ", $bodyText);
        
        if (!is_dir($logDir)) {
			throw new Nette\DirectoryNotFoundException("Directory '$logDir' is not found or is not directory.");
		}
        
        if (!empty($bodyText) && @filemtime($logDir . '/email-sent') + $emailSnooze < time() // @ - file may not exist
			&& @file_put_contents($logDir . '/email-sent', 'sent') // @ - file may not be writable
		) {
            $mail = new Message;
            $mail->setFrom('Admin Gateway <gateway@no-reply.com>')
                ->addTo('nikola.badev@ec-quadrat.com')
				->addTo('darkokrstev@gmail.com')
                ->setSubject('GW Handler Problems')
                ->setBody("".$bodyText)
                ->send();			
		}
        //END of SEND E-mail    
    
        // basic schedule selection
        $schedules = $this->getService('database')
                ->table('gw_schedule')
                ->where(array('status' => 'new', 'is_cancelled' => 0));      
        
        // a specific schedule requested (means manually executed)?
        if ($scheduleId) {
            $this->logger->logMessage(\Logger\ILogger::WARNING, sprintf("MANUAL EXECUTION: Process '%s' was started manually, not by CRON job...", $scheduleId));
            
            // just search for specific new and not cancelled
            $schedules->where(array('id' => $scheduleId));
        } else {
            $this->logger->logMessage(\Logger\ILogger::DEBUG,'CRON JOB: Searching for new schedules...');
            
            // SELECT * FROM `gw_schedule` 
            // WHERE status = "new" 
            // AND is_cancelled = 0 
            // AND gw_connection_id NOT IN (SELECT gw_connection_id 
            // FROM `gw_schedule` 
            // WHERE status = "processing" AND is_cancelled = 0 AND is_archived = 0)
            // GROUP BY gw_connection_id
            // ORDER BY id ASC
            
            // append condition to avoid process another schedule 
            // when there is any other running for the same connection
            $processingSchedules = $this->getService('database')
                        ->table('gw_schedule')
                        ->select('gw_connection_id')
                        ->where(array('status' => 'processing', 'is_cancelled' => 0, 'is_archived' => 0));

            // per connection restriction
            if ($processingSchedules->count()) {
                $schedules->where(array('gw_connection_id NOT' => array_keys($processingSchedules->fetchPairs('gw_connection_id'))));
            }
        
            $schedules->group('gw_connection_id');
        }
        
        // no schedule(s) found
        if (!$schedules->count()) {
            if ($scheduleId) {
                $this->logger->logMessage(sprintf("Schedule '%s' has been already processed or does not exists.", $scheduleId));
            } else {
                $this->logger->logMessage(\Logger\ILogger::DEBUG, 'CRON JOB: There is no new schedule to process or some is currently running.');
            }
        }

        // we go through all schedules and process each
        foreach ($schedules as $schedule) {
            
            $this->logger->logMessage("====================== BEGIN (schedule id = " . $schedule->id .") ==================");

            // process specific connection
            // FIXME load before loop
            $connection = $this->getService('database')
                    ->table('gw_connection')
                    ->select('gw_connection.*, gw_schedule:gw_source:*, gw_schedule:gw_source:gw_handler.class, gw_schedule:gw_source:gw_handler.node')
                    ->where(array('gw_connection.id' => $schedule->gw_connection_id))
                    ->where(array('gw_schedule:id' => $schedule->id))
                    ->fetch(); // DB record connection
            
            Utils::setConnectionLogDir($connection->name);
            
            // BEGIN: update scheduler about begun
            $this->notifyScheduler($schedule->id);

            // according to source type (file, filepath, filename, ...) loads the content
            $source = array($connection->class => $this->_prepareSource($connection->content, $connection->type));
            $destination = $connection->node == Connection::NODE_SHOP ? Connection::NODE_ERP : Connection::NODE_SHOP;
            
            // try to process connection
            if ($this->processConnection($connection->name, $source, $destination)) {
                $this->logger->logMessage('Execution finished successfully. Updating scheduler ...');
                
                // SUCCESS: update scheduler about success 
                $this->notifyScheduler($schedule->id, true);
                
                $this->logger->logMessage('Scheduler updated.');
                
                $this->flashMessage("Execution finished successfully.", 'success');
            } else {
                // FAIL:: update scheduler about fail
                
                $this->logger->logMessage('Error during processing execute action. Updating scheduler...');
                
                $this->notifyScheduler($schedule->id, true, true);
                $this->logger->logMessage('Scheduler updated.');

                $this->logger->logMessage('Cancelled with error.');
                $this->flashMessage("Error during processing 'execute' action (see log for details).", 'error');
            }
            
            $this->logger->logMessage("Schedule ID = '%s' completed.", $schedule->id);
            $this->logger->logMessage("===================== END  (schedule id = " . $schedule->id .") =====================");
        }

        // FIXME Magmi outputs something, so session cannot start and we cannot redirect (headers already sent)
        //$this->redirect(':Admin:Schedule:default');
    }

    /**
     * Process specific connection.
     * 
     * @param string|Gateway\Connection $conn
     * @param array $source
     * @param string $destination
     */
    protected function processConnection($conn, $source = array(), $destination = Connection::NODE_SHOP) {
        // name was given therefore connection must be loaded
        if (is_string($conn)) {
            $conn = $this->connections->get($conn); // Gateway\Connection instance
        }

        // connection does not exist
        if (!$conn) {
            throw new \Nette\InvalidArgumentException("Connection does not exists.");
        }

        $connName = $conn->name;
        
        // specific source given (means do not use default one)
        if (count($source)) {
            $this->source = $source;
        }

        $this->logger->logMessage(sprintf("Processing connection '%s'...", $connName));

        try {
            // if no input specified, we cannot continue to process the connection
            if (!$this->hasSource()) {
                throw new \Nette\IOException("Cannot continue... Trying to process connection with no source.");
            }

            // read input data, process them and create DataSources
            $fromErpToShop = $destination == Connection::NODE_SHOP ? true : false;
            
            $this->logger->logMessage(sprintf("Destination: %s", $destination));

            // load connection's handler (both readers and writers)
            $readers = $conn->getHandlers($fromErpToShop ? Connection::NODE_ERP : Connection::NODE_SHOP, IConnection::STREAM_READER);
			$writers = $conn->getHandlers($destination, IConnection::STREAM_WRITER);
			
			// go through all writer handlers (products, customers etc.) and pass reader datasource 
            foreach ($writers as $wKey => $writer) {
                // if reader handler for this datasource exists
                if (isset($readers[$wKey]) && $readers[$wKey]) {
                    // getting reader for this writer
                    $reader = $readers[$wKey];
					
                    // retrieve source to read
                    $source = $this->getSource($reader);
					
                    if ($source) {
                        // source found, we pass it to be read
                        $this->logger->logMessage(sprintf("Source input passing to '%s' input.", get_class($reader)));

                        $reader->setInput($source);
                    } else {
                        // other ones are skipped
                        $this->logger->logMessage(\Logger\ILogger::DEBUG, sprintf("SKIPPING: No source given for '%s' reader.", get_class($reader)));
                        continue;
                    }
					
                    // transforming source to general datasource 
                    $ds = $reader->getDataSource();

					// and passing general datasource to writer to transform it to the destination's node format
                    $this->logger->logMessage("Passing processed datasource from '%s' to '%s' writer.", get_class($reader), get_class($writer));

                    $writer->setDataSource($ds);
					
					$affectedAmount = $writer->process();
                   
					// FIXME ugly!!! call this from Magento writers
                    // REINDEXER 1: init reindexer
                    // DISABLE REINDEXING if STOCK handler in use - NB 08.11.2013
                    
                
                    $reindexer = null;
                    $reindex_start = true;
                                        
                    if (strpos(get_class($writer),'Writer\Stock') !== false) {
                        $reindex_start = false;
                        $this->logger->logMessage("Reindexing disabled, STOCK handler is used!");
                    }
                  
                    // init Magento reindexer
                    if ($fromErpToShop && $this->context->parameters['gateway']['reindex'] && $reindex_start) {
                        $this->logger->logMessage("Trying to init reindexing...");

                        $url = $this->getService('database')
                                        ->table('gw_connection')
                                        ->where(array('name' => $conn->name))
                                        ->fetch()->shop_url;
                        
                        if ($url) {
                            $reindexer = new \Gateway\Handler\Shop\Magento\Reindex($url);
                            $code = $reindexer->init();
                            
                            if ($code == 200) {
                                $this->logger->logMessage("Done.");
                            } else {
                                $this->logger->logMessage(\Logger\ILogger::WARNING, sprintf("Reindexing failed (returned %s HTTP code) on '%s' - is script there?", $code, $url));
                                $reindexer = null;
                            }
                        }
                    }
                    
                    // main process
                    $this->logger->logMessage("Executing writer process...");
                    
                    //$affectedAmount = $writer->process();
					
                    $this->logger->logMessage(sprintf("Processed. Affected %s", $affectedAmount));

                    // REINDEXER 2: execute or finished reindexer
                    if ($reindexer && $reindex_start) {
                        // apply indexing only when anything was processed
                        if ($affectedAmount) {
                            $this->logger->logMessage("Trying to execute reindexing...");
                            $code = $reindexer->execute();
                            
                            // code 200 means everything ok, any other code is remote error
                            if ($code == 200) {
                                $this->logger->logMessage("Reindexed.");
                            } else {
                                $this->logger->logMessage("Reindexing failed.");
                            }
                        } else { // do not apply indexing
                            $this->logger->logMessage("No items affected. Disabling reindexing...");
                            $code = $reindexer->finish();
                            
                            // code 200 means everything ok, any other code is remote error
                            if ($code == 200) {
                                $this->logger->logMessage("Disabled.");
                            } else {                                
                                $this->logger->logMessage("Disabling failed.");
                            }
                        }
                    }
                    
                } else {
                    // there is no reader set for this writer - should not happen
                    $this->logger->logMessage(\Logger\ILogger::DEBUG, "SKIPPING: No reader input exists for '%s' writer.", get_class($writer));
                }
            }

            $this->logger->logMessage(sprintf("SUCCESS: Connection '%s' processing completed!", $connName));
        } catch (\Exception $e) {
            $this->logger->logMessage(sprintf("FAIL: Connection '%s' processing interrupted. Reason: " . $e->getMessage(), $connName));

            return false;
        }

        return true;
    }
    
    ////////////////
    // COMPONENTS //
    ////////////////
    /**
     * Source input form.
     * 
     * @return \Nette\Application\UI\Form
     */
    public function createComponentSourceForm() {
        $form = new Form($this, 'sourceForm');

        // data preparing
        $handlers = array();
        $connections = array();

        foreach ($this->connections as $conn) {
            $connections[$conn->name] = $conn->name;
            $handlers = $this->loadSelectHandlers($conn);
        }

        $form->addSelect('connection', 'Use connection', $connections)
                ->setPrompt('- Select connection -')
                ->setRequired('Please, select connection.');
        $form->addSelect('handler', 'Use handler', $handlers)
                ->setPrompt('- Select handler -')
                ->setRequired('Please, select handler.');

        // disable on load
        if (!$form->isSubmitted()) {
            $form['handler']->setDisabled(true);
        } else {
            $conn = $form['connection']->getValue();
            $connection = $this->connections->get($conn);
            $items = $this->loadSelectHandlers($connection);

            $form['handler']->setItems($items);
        }

        // sources
        $container = $form->addContainer('source');
        $container->addTextArea('text', 'Text')->setOption('description', 'Set source as text or...');

        $container->addUpload('file', 'File')->setOption('description', '...upload file as source');

        $container['text']
                ->addConditionOn($form['source']['file'], ~$form::FILLED)
                ->addRule($form::FILLED, "Text or file must be filled.");

        $form->addSubmit('process', 'Process')->setAttribute('class', 'default');
        $form->onSuccess[] = callback($this, 'handleSourceFormSubmitted');

        $form->addProtection('Please submit this form again (security token has expired).');

        return $form;
    }

    /**
     * Passes source directly for execution.
     * 
     * @param \Nette\Application\UI\Form $form
     */
    public function handleSourceFormSubmitted($form) {
        $vals = $form->getValues();

        // get text source as default
        $source = $vals['source']['text'];

        // and if file has been sent, get file
        if ($vals['source']['file']->size) {
            $source = file_get_contents($vals['source']['file']);
        }

        // extract direction
        $from = $this->getService('database')
                ->table('gw_handler')
                ->where(array('class' => $vals['handler']))
                ->fetch();

        $destination = Connection::NODE_ERP;

        if ($from['node'] == Connection::NODE_ERP) {
            $destination = Connection::NODE_SHOP;
        }

        // trying to process connection
        Utils::setConnectionLogDir($vals['connection']);
            
        if ($this->processConnection($vals['connection'], array($vals['handler'] => $source), $destination)) {
            
            $this->flashMessage("Source has been passed successfully.", 'success');
        } else {
            $this->flashMessage("Invalid source or any other error. See log for more information.", 'error');
        }
    }

    /**
     * Loading of handlers via ajax.
     * 
     * @param string $connName
     */
    public function handleSourceFormHandlers($connName) {
        $form = $this->getComponent("sourceForm");

        // data preparing
        $handlers = array();
        $disabled = true;

        // get handlers for this connection
        if ($connName) {
            $conn = $this->connections->get($connName);
            $handlers = $this->loadSelectHandlers($conn);

            $disabled = false;
        }

        $form['handler']->setItems($handlers)
                ->setDisabled($disabled);

        // invalidate snippet
        $this->invalidateControl("sourceFormHandler");
    }

    /**
     * Loads connections handlers for form select.
     * 
     * @param \Gateway\Connection $conn
     * @return array
     */
    protected function loadSelectHandlers($conn) {
        $nodes = array();
        $nodes[IConnection::NODE_ERP] = $conn->getHandlers(IConnection::NODE_ERP, IConnection::STREAM_READER);
        $nodes[IConnection::NODE_SHOP] = $conn->getHandlers(IConnection::NODE_SHOP, IConnection::STREAM_READER);
        $handlers = array();
        
        foreach ($nodes as $key => $node) {
            foreach ($node as $handler) {
                $handlers[get_class($handler)] = $this->_getDirection($key) . ': ' . get_class($handler);
            }
        }

        ksort($handlers);

        return $handlers;
    }

    /**
     * Returns direction type.
     * 
     * @param string $nodeType
     * @return string
     */
    private function _getDirection($nodeType) {
        if ($nodeType == IConnection::NODE_ERP) {
            return IConnection::NODE_ERP . ' -> ' . IConnection::NODE_SHOP;
        } elseif ($nodeType == IConnection::NODE_SHOP) {
            return IConnection::NODE_SHOP . ' -> ' . IConnection::NODE_ERP;
        } else {
            return 'undefined';
        }
    }

    /**
     * According to given type it prepare/loads content to be processed.
     * 
     * @param mixed $content
     * @param string $type
     * @return string
     */
    private function _prepareSource($content, $type) {
        switch ($type) {
            case \Gateway\Handler\ISource::TYPE_FILEPATH:
                $filePath = $content;

                if (!file_exists($filePath)) {
                    throw new \Nette\IOException(sprintf("Source '%s' does not exists in file system.", $filePath));
                }
                
                return file_get_contents($filePath);
                break;
            case \Gateway\Handler\ISource::TYPE_FILE:
            case \Gateway\Handler\ISource::TYPE_TEXT:
                return $content;
                break;
            case \Gateway\Handler\ISource::TYPE_FILENAME:
            case \Gateway\Handler\ISource::TYPE_FOLDER:
            default:
                throw new \Nette\NotSupportedException(sprintf("Source type of '%s' is not supported."));
                break;
        }
    }
    
    /**
     * Checks given source.
     * 
     * @param string $readerName
     * @return boolean
     */
    protected function hasSource($reader = false) {
        if (count($this->source)) {

            // if readerName given and there is input for that reader....
            if ($reader) {
                $reader = is_object($reader) ? get_class($reader) : $reader;

                if (isset($this->source[$reader]) && $this->source[$reader]) {
                    return true;
                }

                return false;
            }

            return true;
        } else {
            return false;
        }

        return false;
    }

    /**
     * Returns reader's input if exists.
     * 
     * @param mixed $reader
     * @return boolea
     */
    protected function getSource($reader) {
        $reader = is_object($reader) ? get_class($reader) : $reader;

        if ($this->hasSource($reader)) {
            return $this->source[$reader];
        }

        return false;
    }

    /**
     * Helper to update scheduled plan state.
     * 
     * @param int $scheduleId
     * @param bool $isFinished
     * @param bool $isCancelled
     */
    protected function notifyScheduler($scheduleId, $isFinished = false, $isCancelled = false) {
        
        try {
            $database = $this->getService('database');
            
            $data = array(
                'status' => $isFinished ? 'finished' : 'processing',
                'is_cancelled' => $isCancelled ? 1 : 0,
            );

            // update finished timestamp
            if ($isFinished) {
                $data['finished_at'] = new \Nette\Database\SqlLiteral('NOW()');
                $this->logger->logMessage(sprintf("Schedule '%s' was finished.", $scheduleId));
            } else {
                $data['executed_at'] = new \Nette\Database\SqlLiteral('NOW()');
                $this->logger->logMessage(sprintf("Schedule '%s' has been executed.", $scheduleId));

                $schedule = $database->table('gw_schedule')
                                    ->where(array('id'=> $scheduleId))
                                    ->fetch();
                
                    $reportData = array(
                        'gw_connection_id' => $schedule->gw_connection_id,
                        'gw_schedule_id' => $scheduleId,
                        'log' => Utils::getLogger()->getFile(),
                    );

                    // pass report data into DB
                    $database->table('gw_report')
                            ->insert($reportData);

                    $this->logger->logMessage(sprintf("Report for schedule '%s' was saved.", $scheduleId));
            }

            // update schedule status
            try {
                $schedule = $database->table('gw_schedule')
                                    ->where(array('id'=> $scheduleId))
                                    ->fetch();
            } catch (\PDOException $e) { // MySQL server has gone away?
                $this->logger->logMessage(\Logger\ILogger::ERROR, $e->getMessage());                
                $this->logger->logMessage(\Logger\ILogger::WARNING, 'Connection lost during import, reconnecting to MySQL database...');
                
                // FIXME this is the workaround to fix MySQL server time out problems - we have to reconnect again 
                // when processing lasts more than set time out limit
                $db = $this->context->parameters['database'];
                $dsn = sprintf("%s:host=%s;dbname=%s", $db['driver'], $db['host'], $db['dbname']);
                $user = $db['user'];
                $password = $db['password'];

                $database = new \Nette\Database\Connection($dsn, $user, $password);

                $schedule = $database->table('gw_schedule')
                                    ->where(array('id'=> $scheduleId))
                                    ->fetch();
            }

            // update state
            $schedule->update($data);

        } catch (\Exception $e) {
            $this->logger->logMessage(\Logger\ILogger::ERROR, sprintf("Report for schedule '%s' could not be saved: '%s'", $scheduleId, $e->getMessage()));                
        }
    }

}
