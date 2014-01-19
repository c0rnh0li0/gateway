<?php

namespace AdminModule\Component;

use Yourface\NiftyGrid\Grid,
    Nette\Diagnostics\Debugger,
    Nette\Utils\Html;

/**
 * Schedule plans of data synchronization.
 *
 * @author Lukas Bruha
 */
class ScheduleGrid extends Grid {

    /**
     * Grid type switcher.
     * 
     * @var bool
     */    
    protected $simple;
    
    /**
     * Helper holding existing reports.
     * 
     * @var array 
     */
    protected $existingReports = array();

    /**
     * Loads existing reports and set type.
     * 
     * @param mixed $data
     * @param bool $simple
     */
    public function __construct($data, $simple = false) {
        parent::__construct($data);

        $this->simple = $simple;
        
        $this->existingReports = array_keys(\Nette\Environment::getService('database')
                                        ->table('gw_report')
                                        ->select('gw_schedule_id')
                                        ->fetchPairs('gw_schedule_id'));
    }

    /**
     * Grid init.
     * 
     */
    protected function init() {
        $self = $this;

        $id = $this->addColumn('id', 'ID');
                
        if (!$this->simple) {
            $id->setTableName('gw_schedule.id')
                ->setTextFilter();
        }
        
        $connName = $this->addColumn('gw_connection.name', 'Connection')->setRenderer(function($row) use ($self) {
                    return Html::el("a")->setHref($self->presenter->link("Setup:", array('id' => $row['connection_id'])))->setText($row['name']);
                });

        if (!$this->simple) {
            $connections = $this->presenter
                    ->getService('database')
                    ->table('gw_connection')
                    ->fetchPairs('name', 'name');
            
            $connName->setSelectFilter($connections);
        }

        $handlers = $this->addColumn('gw_source:gw_handler.type', 'Handles')->setRenderer(function($row) use ($self) {
                    $link = $self->presenter->link("source", array('id' => $row['id']));

                    $html = Html::el("small")->setHtml("(" . Html::el("a")->setHref($link)->setText("view source") . ")");
                    $line = Html::el('br');
                    $descLink = $self->presenter->link("Setup:", array('id' => $row['connection_id'], 'highlightHandler' => $row['handler_id']));
                    $descHtml = Html::el("a")->setHref($descLink)->setText("detail");
                    $desc = Html::el("small")->class('info')->setHtml($row['handler_description'] . " (" . $descHtml . ")");
                    
                    $res = $row['type'] . " " . $html . $line . $desc;

                    return Html::el('span')->setHtml($res);
                });

        if (!$this->simple) {
            $handlerTypes = $this->presenter
                ->getService('database')
                ->table('gw_handler_type')
                ->fetchPairs('name', 'name');
            
            $handlers->setSelectFilter($handlerTypes);
        }

        $this->addColumn('inserted_at', 'Inserted at')
                ->setRenderer(function($row) {
                            $el = Html::el('span')->setTitle($row['inserted_at'])->setText(\Yourface\Utils\Helpers::ago($row['inserted_at'], 3600));

                            if (!$row['executed_at'] && !$row['finished_at'] && $row['is_cancelled']) {
                                $el->setClass('suppress');
                            }

                            return $el;
                        });
        $this->addColumn('executed_at', 'Process started at')
                ->setRenderer(function($row) {
                            return Html::el('span')->setTitle($row['executed_at'])->setText(\Yourface\Utils\Helpers::ago($row['executed_at'], 3600));
                        });
        $this->addColumn('finished_at', 'Completed at')
                ->setRenderer(function($row) {
                            return Html::el('span')->setTitle($row['finished_at'])->setText(\Yourface\Utils\Helpers::ago($row['finished_at'], 3600));
                        });
        $this->addColumn('status', 'State')
                ->setRenderer(function($row) {
                            $el = $row['status'];
                            
                            if (!$row['executed_at'] && !$row['finished_at'] && $row['is_cancelled']) {
                                $el = \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['status']);
                            }
                         
                            return $el;
                        });

        if (!$this->simple) {
            $this->addColumn('is_cancelled', 'Status')
                    ->setRenderer(function($row) {
                                if ($row['finished_at']) {
                                    $value = \Nette\Utils\Html::el('span')->setText($row['is_cancelled'] ? "yes" : "no");

                                    return \Nette\Utils\Html::el('span')->setClass(array('icon', $row['is_cancelled'] ? "no" : "yes"))->setHtml($value);
                                }
                            })
                     ->setSelectFilter(array(0 => 'success', 1 => 'fail'));

            $this->addButton("execute")
                    ->setClass("execute")
                    ->setAjax(false)
                    ->setText(function($row) use ($self) {
                                if (!$row['executed_at'] && !$row['finished_at'] && !$row['is_cancelled']) {
                                    return "Execute";
                                }
                            })
                    ->setLink(function($row) use ($self) {
                                if (!$row['executed_at'] && !$row['finished_at'] && !$row['is_cancelled']) {
                                    return $self->link("execute", $row['id']);
                                }
                            });

            $this->addButton("cancel")
                    ->setClass("cancel")
                    ->setAjax(false)
                    ->setText(function($row) use ($self) {
                                if (!$row['executed_at'] && !$row['finished_at']) {
                                    if (!$row['is_cancelled']) {
                                        return "Cancel";
                                    } else {
                                        return "Reload";
                                    }
                                }
                            })
                    ->setLink(function($row) use ($self) {
                                if (!$row['executed_at'] && !$row['finished_at']) {
                                    if (!$row['is_cancelled']) {
                                        return $self->link("cancel", $row['id']);
                                    } else {
                                        return $self->link("reload", $row['id']);
                                    }
                                }
                            });

            $this->addButton("finish")
                    ->setClass("finish")
                    ->setAjax(false)
                    ->setText(function($row) use ($self) {
                                // inserted, processed but not finished
                                if ($row['inserted_at'] && $row['executed_at'] && !$row['finished_at']) {
                                    if (!$row['is_cancelled']) {
                                        return "Force finish";
                                    }
                                }
                            })
                    ->setLink(function($row) use ($self) {
                                // inserted, processed but not finished
                                if ($row['inserted_at'] && $row['executed_at'] && !$row['finished_at']) {
                                    if (!$row['is_cancelled']) {
                                        return $self->link("finish", $row['id']);
                                    }
                                }
                            });
                          

            $this->addButton("report")
                    ->setClass("report")
                    ->setAjax(false)
                    ->setText(function($row) use ($self) {                                
                                if (!$row['executed_at'] && !$row['finished_at']) {
                                    
                                } else {
                                    if (in_array($row['id'], $self->getExistingReports())) {
                                        return "Report";
                                    }
                                }
                            })
                    ->setLink(function($row) use ($self) {
                                if (!$row['executed_at'] && !$row['finished_at']) {
                                    
                                } else {
                                    if (in_array($row['id'], $self->getExistingReports())) {
                                        return $self->presenter->link("detail", $row['id']);
                                    }
                                }
                            });

            $this->addButton("restart")
                    ->setClass("cancel")
                    ->setAjax(false)
                    ->setText(function($row) use ($self) {
                                if (($row['finished_at'] && $row['is_cancelled']) || ($row['executed_at'] && !$row['finished_at'])) {
                                    return "Restart";
                                }
                            })
                    ->setLink(function($row) use ($self) {
                                if (($row['finished_at'] && $row['is_cancelled']) || ($row['executed_at'] && !$row['finished_at'])) {
                                    return $self->link("restart", $row['id']);
                                }
                            })
                    ->setConfirmationDialog(function($row) {
                            return sprintf("Are you sure you want to restart schedule no. %s?", $row['id']);
                        });


            $this->addAction("archive", "Move to archive")
                    ->setCallback(function($id) use ($self) {
                                return $self->handleArchive($id);
                            });
                            
            $this->addAction("delete", "Remove")
                    ->setCallback(function($id) use ($self) {
                                return $self->handleRemove($id);
                            });                              
        }
    }

    /**
     * Moves schedule to archive.
     * 
     * @param int $id
     */
    public function handleArchive($id) {
        try {
            $this->presenter->getService('database')
                    ->table('gw_schedule')
                    ->where(array('id' => $id, 'status' => 'finished'))
                    ->update(array('is_archived' => 1));
            $this->presenter->flashMessage(sprintf("Schedule(s) '%s' was/were moved to archive.", implode(",", $id)), 'success');
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot move selected to archive. Was schedule finished already?', 'error');
        }

        $this->redirect('this');
    }
    
    /**
     * Removes the entries.
     * 
     * @param int $id
     */
    public function handleRemove($id) {
        try {
            $this->presenter->getService('database')
                    ->table('gw_schedule')
                    ->where(array('id' => $id, 'status' => 'finished'))
                    ->delete();
            $this->presenter->flashMessage(sprintf("Schedule(s) '%s' was/were DELETED.", implode(",", $id)), 'error');
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot move selected to archive. Was schedule finished already?', 'error');
        }

        $this->redirect('this');
    }     

    /**
     * Set schedule plan as being cancelled.
     * 
     * @var int $id
     */    
    public function handleCancel($id) {
        try {
            $this->presenter->getService('database')
                    ->table('gw_schedule')
                    ->where(array('id' => $id, 'status' => 'new'))
                    ->update(array('is_cancelled' => 1));
            $this->presenter->flashMessage(sprintf("Schedule(s) '%s' was/were cancelled.", implode(",", (array) $id)), 'success');
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot cancel selected. Process is already running?', 'error');
        }

        $this->redirect('this');
    }

    /**
     * Set schedule plan as being finished.
     * 
     * @var int $id
     */
    public function handleFinish($id) {
        try {
            $this->presenter->getService('database')
                    ->table('gw_schedule')
                    ->where(array('id' => $id, 'status' => 'processing'))
                    ->update(array(
                        'is_cancelled' => 1,
                        'finished_at' => new \Nette\Database\SqlLiteral('NOW()'),
                        'status' => 'finished'
                    ));
            $this->presenter->flashMessage(sprintf("Schedule(s) '%s' was/were cancelled and finished.", implode(",", (array) $id)), 'warning');
            
            \Gateway\Utils::log(sprintf("Schedule %s was force finished.", $id));            
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot finish selected. Process is already finished?', 'error');
        }

        $this->redirect('this');
    }

    /**
     * Set schedule plan as not being cancelled.
     * 
     * @var int $id
     */    
    public function handleReload($id) {
        try {
            $this->presenter->getService('database')
                    ->table('gw_schedule')
                    ->where(array('id' => $id))
                    ->update(array('is_cancelled' => 0));
            $this->presenter->flashMessage(sprintf("Schedule(s) '%s' was/were reload.", implode(",", $id)), 'success');
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot reload selected.', 'error');
        }

        $this->redirect('this');
    }

    /**
     * Set schedule plan as being executed and calls execute action on presenter.
     * 
     * @var int $id
     */    
    public function handleExecute($id) {
        $params = array('action' => 'execute', 'scheduleId' => $id);

        // process subrequest
        $request = new \Nette\Application\Request('Default:execute', 'GET', $params);
        $presenter = new \DefaultPresenter($this->presenter->getContext());
        $response = $presenter->run($request);

        $this->presenter->flashMessage(sprintf("Schedule '%s' has been executed.", $id));

        //$this->presenter->redirect('this');
    }
    
    /**
     * Set schedule plan as being new.
     * 
     * @var int $id
     */    
    public function handleRestart($id) {
        try {
            $this->presenter->getService('database')
                    ->table('gw_schedule')
                    ->where(array('id' => $id))
                    ->update(array('is_cancelled' => 0, 'executed_at' => NULL, 'finished_at' => NULL, 'status' => 'new'));
            $this->presenter->flashMessage(sprintf("Schedule(s) '%s' will be processed again.", $id), 'success');
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot restart schedule.', 'error');
        }

        $this->redirect('this');
    }    

     /**
     * Existing reports getter.
     * 
     * @return array
     */
    public function getExistingReports() {
        return $this->existingReports;
    }
    
}