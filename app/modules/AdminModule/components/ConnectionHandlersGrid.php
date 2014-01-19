<?php

namespace AdminModule\Component;

use \Yourface\NiftyGrid\Grid;

/**
 * A list of connection handlers.
 *
 * @author Lukas Bruha
 */
class ConnectionHandlersGrid extends Grid {

    /**
     * Change default datasource to array.
     * 
     * @return \Yourface\NiftyGrid\ArrayDataSource
     */
    protected function getDefaultDataSource() {
        return new \Yourface\NiftyGrid\ArrayDataSource($this->data);
    }

    /**
     * Grid init.
     * 
     */
    protected function init() {
        $this->enableSorting = FALSE;
        $this->paginate = FALSE;

        $this->addColumn('type', 'Handling')
                  ->setRenderer(function($row) {
                      $res = \Nette\Utils\Html::el()->setText($row['type']);
                      
                      $res .= \Nette\Utils\Html::el('br');
                      $res .= \Nette\Utils\Html::el('small')->setText($row['direction']);
                      $res .= \Nette\Utils\Html::el('br');
                                $res .= "&nbsp;";
                      
                      return \Nette\Utils\Html::el('')->setHtml($res);
                  });
        $this->addColumn('reader_description', 'Read via')
                ->setRenderer(function($row) {
                            $res = $row['reader_description'];
                            $highlight = \Nette\Environment::getHttpRequest()->getQuery('highlightHandler') == $row['adapter_reader_id'];
                            
                            if ($highlight) {
                                $res = \Nette\Utils\Html::el('b')->setText($row['reader_description']);    
                            }
                    
                            $res .= \Nette\Utils\Html::el('br');
                            $res .= \Nette\Utils\Html::el('small')->setText($row['reader_class']);
                            $res .= \Nette\Utils\Html::el('br');
                            
                            if (($row['adapter_reader_settings'])) {
                                $res .= \Nette\Utils\Html::el('small')->setClass('settings')->setText($row['adapter_reader_settings']);
                            } elseif ($row['adapter_writer_settings']) {
                                $res .= "&nbsp;";
                            }

                            return \Nette\Utils\Html::el('span')->setHtml($res);
                        });

        $this->addColumn('writer_description', 'Write via')
                ->setRenderer(function($row) {
                            $res = $row['writer_description'];
                            $res .= \Nette\Utils\Html::el('br');
                            $res .= \Nette\Utils\Html::el('small')->setText($row['writer_class']);

                            $res .= \Nette\Utils\Html::el('br');
                            if (($row['adapter_writer_settings'])) {
                                $res .= \Nette\Utils\Html::el('small')->setClass('settings')->setText($row['adapter_writer_settings']);
                            } elseif ($row['adapter_reader_settings']) {
                                $res .= "&nbsp;";
                            }

                            return \Nette\Utils\Html::el('span')->setHtml($res);
                        });

        $self = $this;

        // remove
                        
        $this->addButton("edit")
                ->setClass("edit")
                ->setText("edit")
                ->setAjax(false)
                ->setLink(function($row) use ($self) {
                            return $self->presenter->link("editHandler", array($row['adapter_reader_id'], $row['adapter_writer_id']));
                        });
                                
        $this->addButton("remove")
                ->setClass("remove")
                ->setText("remove")
                ->setAjax(false)
                ->setLink(function($row) use ($self) {
                            return $self->link("delete", array($row['id'], $row['adapter_reader_id'], $row['adapter_writer_id']));
                        });

    }

    /**
     * Deletes handlers.
     * 
     * @param int $id
     */
    public function handleDelete($connectionId, $readerId, $writerId) {
        $fetch = $this->presenter->getService('database')
                ->table('gw_adapter')
                ->where('gw_handler_id', array($readerId, $writerId))
                ->where('gw_connection_id', $connectionId)
                ->delete();

        $this->presenter->flashMessage("Handler has been removed.", 'success');

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('this');
        }
    }

}