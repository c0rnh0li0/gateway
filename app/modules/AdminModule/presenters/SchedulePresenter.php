<?php

namespace AdminModule;

use Nette\Diagnostics\Debugger,
            Nette\Application\UI\Form,
    Gateway\Connection,
    Gateway\Connections,
    Gateway\IConnection;

/**
 * Scheduled plans execution.
 * 
 * @author Lukas Bruha
 * 
 */
class SchedulePresenter extends BasePresenter {

    /**
     * @var \Nette\Database\Selection
     * 
     */
    protected $schedule;

    /**
     * Loads all plan.
     * 
     */
    public function startup() {
        parent::startup();

        $this->schedule = $this->getService('database')
                                ->table('gw_schedule')
                                ->select('gw_schedule.*, 
                                            gw_connection.name, 
                                            gw_connection.id AS connection_id, 
                                            gw_source:gw_handler.id AS handler_id,
                                            gw_source:gw_handler.type,
                                            gw_source:gw_handler.description AS handler_description')
                                ->where('gw_schedule.is_archived != ?', 1);
    }

    /**
     * Invalidates grid on ajax request.
     * 
     */
    public function actionDefault() {    
        if ($this->isAjax()) {
            $this->getComponent('scheduleGrid')->invalidateControl();
        }
    }
    
    /**
     * Shows schedule detail.
     * 
     * @param int $id
     */
    public function actionDetail($id) {
        $schedule = $this->getService('database')
                            ->table('gw_report')
                            ->select('gw_report.*, gw_schedule.gw_connection.name, gw_schedule.*')
                            ->where(array('gw_report.gw_schedule_id' => $id))
                            ->fetch();
        
        if (!$schedule) {
            $this->flashMessage(sprintf("No report for schedule '%s' exists.", $id), 'error');
            $this->redirect('default');
        }
        
        
        // load file log
        $file = false;
        if (file_exists($schedule->log)) {
            $file = @file_get_contents($schedule->log);
        }
        
        $this->template->logFile = $file; 
        $this->template->report = $schedule; 
        $this->template->processLength = gmdate('H:i:s', strtotime($schedule->finished_at) - strtotime($schedule->executed_at));
    }
    
    /**
     * Shows schedule source.
     * 
     * @param int $id
     */
    public function actionSource($id) {
        $source = $this->getService('database')
                            ->table('gw_source')
                            ->where(array('gw_schedule_id' => $id))
                            ->fetch();
        
        $this->template->source = $source;
    }
    
    /**
     * Downloads schedule source (available only for filepath type).
     * 
     * @param int $id
     */
    public function actionDownload($id) {
        $source = $this->getService('database')
                    ->table('gw_source')
                    ->where(array('gw_schedule_id' => $id))
                    ->fetch();
        
        // source type check
        if ($source->type != \Gateway\Handler\ISource::TYPE_FILEPATH) {
            $this->flashMessage(sprintf("Source type of '%s' is not downloadable.", $source->type), 'error');
            $this->redirect('default');
        }
        
        // expected filepath
        $filepath = $source->content;
        
        if (!file_exists($filepath)) {
            $this->flashMessage("File does not exists.", 'error');
            $this->redirect('default');
        }
        
        $filename = basename($filepath);
        
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Transfer-Encoding: binary");
        header('Content-Description: File Transfer');
        header("Content-Type: application/octet-stream");
        header("Content-Length: ".filesize($filepath));
        header("Content-Disposition: attachment; filename=" .$filename.";");
        ob_clean();
        flush();
        readfile($filepath);
        
        $this->terminate();
    }
    
    ////////////////
    // COMPONENTS //
    ////////////////
    /**
     * Loads schedule list.
     * 
     * @return \AdminModule\Component\ScheduleGrid
     */
    public function createComponentScheduleGrid() {
        $grid = new \AdminModule\Component\ScheduleGrid($this->schedule);
        $grid->setDefaultOrder('id DESC');
        return $grid;
    }
    
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
        $form->addTextArea('text', 'Text')->setOption('description', 'or just put text with source.');
        
        $form->addUpload('file', 'File')->setOption('description', 'Upload file with source...');
        
        $form['text']
                ->addConditionOn($form['file'], ~$form::FILLED)
                ->addRule($form::FILLED, "Text or file must be filled.");

        $form->addSubmit('process', 'Put to schedule')->setAttribute('class', 'default');
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
        
        $reader = $this->getService('database')
                ->table('gw_handler')
                ->where(array('class' => $vals['handler']))
                ->fetch();
        
        $node = $reader->node;
        $type = \Nette\Utils\Strings::firstUpper($reader->type);
        
        $params = array(
                    'action' => 'post',
                    'connection' => $vals['connection'],
                    'node' => $node
                );
        
        $files = array();
        $post = array();
        
        
        if ($vals['file'] && $vals['file']->size) {
            $files['file'] = $vals['file'];
        } elseif ($vals['text']) {
            $post['text'] = $vals['text'];
        } else {
            $this->flashMessage('Either text or file must be filled in form.', 'error');
            $this->forward('this');
        }
                
        // process subreq
        $request = new \Nette\Application\Request(':Api:' . $type, 'POST', $params, $post, $files);
        $presenter = new \ApiModule\ProductsPresenter($this->getContext());
        $response = $presenter->run($request);

        $this->flashMessage($response->getSource());
        $this->redirect('this');
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
    protected function loadSelectHandlers(\Gateway\Connection $conn) {
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

}
