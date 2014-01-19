<?php

namespace AdminModule\Component;

use \Yourface\NiftyGrid\Grid,
    Nette\Diagnostics\Debugger,
    Nette\Utils\Html;

/**
 * Mapping rules overview.
 *
 * @author Lukas Bruha
 */
class MappingRulesGrid extends Grid {

    /**
     * Columns init.
     * 
     */
    protected function beforeInit() {
        $this->addColumn('old_name', 'ERP name')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['old_name'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['old_name']);
                        })                
                ->setTextEditable();
                        
        $this->addColumn('old_value', 'ERP value')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['old_value'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['old_value']);
                        })                                
                ->setTextEditable();
                        
        $this->addColumn('new_name', 'Shop name')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['new_name'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['new_name']);
                        }) 
                ->setTextEditable();
        
        $this->addColumn('new_value', 'Shop value')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['new_value'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['new_value']);
                        })       
                ->setTextEditable();

        $this->addColumn('is_enabled', 'Enabled?')
                ->setRenderer(function($row) {
                            $value = \Nette\Utils\Html::el('span')
                                    ->setText($row['is_enabled'] ? "yes" : "no");
                    
                            return \Nette\Utils\Html::el('span')
                                    ->setClass(array('icon', $row['is_enabled'] ? "enabled" : "disabled"))
                                    ->setHtml($value);
                        })
                ->setBooleanEditable();
    }

    /**
     * Grid init.
     * 
     */    
    protected function init() {
        $self = $this;

        // inline edit
        $this->addButton(Grid::ROW_FORM, "Inline edit")
                ->setText('edit')
                ->setClass("inline-edit");

        $this->setRowFormCallback(function($values) use ($self) {
                    $values['is_enabled'] = isset($values['is_enabled']) && ($values['is_enabled'] == "on") ? 1 : 0;

                    $self->presenter->getService('database')
                            ->table('gw_mapping_rule')
                            ->where(array('id' => $values['id']))
                            ->update($values);

                    $self->presenter->flashMessage("Mapping rules were updated.", 'success');

                    if (!$self->presenter->isAjax()) {
                        $self->redirect('this');
                    }
                });

        // enabled?
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


        // delete
        $this->addButton("delete")
                ->setClass("delete")
                ->setText("delete")
                ->setLink(function($row) use ($self) {
                            return $self->link("delete!", $row['id']);
                        })
                ->setConfirmationDialog(function($row) {
                            return sprintf("Are you sure you want to remove '%s'/'%s' mapping?", $row['old_name'], $row['new_name']);
                        });

        // common delete
        $this->addAction("delete", "delete")
                ->setCallback(function($id) use ($self) {
                            return $self->handleDelete($id);
                        });
    }

    /**
     * Deletes mapping rule(s).
     * 
     * @param int $id
     */
    public function handleDelete($id) {

        try {
            $this->presenter->getService('database')
                    ->table('gw_mapping_rule')
                    ->where(array('id' => $id))
                    ->delete();
            $this->presenter->flashMessage("Mapping rule(s) was deleted.", 'success');
        } catch (\PDOException $e) {
            $this->presenter->flashMessage('Cannot delete mapping rule(s).', 'error');
        }

        if (!$this->presenter->isAjax()) {
            $this->redirect('this');
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
                ->table('gw_mapping_rule')
                ->find($id)
                ->update(array('is_enabled' => $isEnabled ? 1 : 0));

        $this->presenter->flashMessage($isEnabled ? "Mapping rule has been enabled." : "Mapping has been disabled.", 'success');

        if (!$this->presenter->isAjax()) {
            $this->redirect('this');
        }
    }

}