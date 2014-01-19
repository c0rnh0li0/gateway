<?php

namespace AdminModule\Component;

use \Yourface\NiftyGrid\Grid,   
    \Nette\Utils\Html;

/**
 * Connections overview.
 *
 * @author Lukas Bruha
 */
class ConnectionsGrid extends Grid {

    /**
     * Grid init.
     * 
     */
    protected function init() {
        $self = $this;

        // columns
        $this->addColumn('id', 'ID');

        $this->addColumn('name', 'Name')
                ->setRenderer(function($row) use ($self){
                            $connName = $row['name'];
                    
                            if ($row['is_enabled']) {
                                $ret = Html::el("a")->setHref($self->presenter->link("Setup:default", array('id' => $row['id'])))->setHtml($connName);
                            } else {
                                $ret = Html::el('span')->setClass('suppress')->setText($connName);
                            }
                            
                            return $ret->setTitle($row['description']);
                        });

        $this->addColumn('shop_url', 'Shop URL')
                ->setRenderer(function($row) {
                            if ($row['is_enabled']) {
                                $url = Html::el('a')->setHref($row['shop_url'])->setText($row['shop_url'])->setClass('external')->setTarget('_blank');
                            } else {
                                $url = \Nette\Utils\Html::el('span')->setClass('suppress')->setHtml($row['shop_url']);
                            }
                            
                            return $url;
                        });

        $this->addColumn('created_at', 'Created at')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['created_at'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['created_at']);
                        });

        $this->addColumn('is_enabled', 'Enabled?')
                ->setRenderer(function($row) {
                            $value = \Nette\Utils\Html::el('span')->setText($row['is_enabled'] ? "yes" : "no");
                    
                            return \Nette\Utils\Html::el('span')
                                    ->setClass(array('icon', $row['is_enabled'] ? "yes" : "disabled"))
                                    ->setHtml($value);
                        });

        // buttons
        $self = $this;
        
        $this->addButton("setup")
                ->setAjax(false)
                ->setClass("setup")
                ->setText("setup")
                ->setLabel("setup adapters, attributes, localization and other mapping")
                ->setLink(function($row) use ($self) {
                            return $self->presenter->link("Setup:default", array('id' => $row['id']));
                        });

        $this->addButton("is_enabled")
                ->setClass("toggle")
                ->setText(function($row) use ($self) {
                            if ($row['is_enabled']) {
                                return "disable";
                            } else {
                                return "enable";
                            }
                        })
                ->setLink(function($row) use ($self) {
                            return $self->link("toggle!", $row['id'], $row['is_enabled'] ? false : true);
                        });

        $this->addButton("delete")
                ->setClass("delete")
                ->setText("delete")
                ->setLink(function($row) use ($self) {
                            return $self->link("delete", $row['id']);
                        })
                ->setConfirmationDialog(function($row) {
                            return sprintf("Are you sure you want to delete '%s' connection?", $row['name']);
                        });
                        
                        
        $this->addButton("clone")
                ->setClass("clone")
                ->setText("clone")
                ->setAjax(false)
                ->setLink(function($row) use ($self) {
                            return $self->presenter->link("clone", $row['id']);
                        })
                ->setConfirmationDialog(function($row) {
                            return sprintf("Are you sure you want to clone '%s' connection?", $row['name']);
                        });

    }

    /**
     * Deletes connection.
     * 
     * @param int $id
     */
    public function handleDelete($id) {
        $row = $this->presenter->getService('database')
                ->table('gw_connection')
                ->find($id);

        if (!$row) {
            $this->presenter->flashMessage('No such record.', 'error');
        } else {
            $row->delete();
            $this->presenter->flashMessage("Connection has been deleted.", 'success');
        }

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('default');
        }
    }

    /**
     * Enables or disables connection.
     * 
     * @param int $id
     * @param bool $isEnabled
     */
    public function handleToggle($id, $isEnabled) {
        $this->presenter->getService('database')
                ->table('gw_connection')
                ->find($id)
                ->update(array('is_enabled' => $isEnabled ? 1 : 0));

        $this->presenter->flashMessage($isEnabled ? "Connection has been enabled." : "Connection has been disabled.", 'success');

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('default');
        }
    }

}