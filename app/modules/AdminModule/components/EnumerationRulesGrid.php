<?php

namespace AdminModule\Component;

use NiftyGrid\Grid,
    Nette\Diagnostics\Debugger,
    Nette\Utils\Html;

/**
 * Localization mapping rules overview.
 *
 * @author Lukas Bruha
 */
class EnumerationRulesGrid extends \AdminModule\Component\LocalizationRulesGrid {

    /**
     * Before parent grid init we have to redefine some columns.
     * 
     */
    protected function beforeInit() {
        $this->addColumn('gw_handler_type_id', 'When handling')
                ->setRenderer(function($row) {
                            $span = \Nette\Utils\Html::el('span')->setText($row['gw_handler_type_id'] ? $row['gw_handler_type_id'] : "- all -");
                                   
                            if (!$row['is_enabled']) {
                                $span->setClass('suppress');
                            }
                            
                            return $span;
                        });

        $scopes = $this->presenter->getService('database')
                ->table('gw_mapping_scope')
                ->where(array('name NOT' => array('product.attributes', '*.lang')))
                ->fetchPairs('id', 'name');

        $this->addColumn('gw_mapping_scope_id', 'Only for')
                ->setRenderer(function($row) {
                            $span = \Nette\Utils\Html::el('span')->setText($row['gw_mapping_scope_id'] ? $row['gw_mapping_scope_id'] : "- all -");
                    
                            if (!$row['is_enabled']) {
                                $span->setClass('suppress');
                            }
                            
                            return $span;
                        })
                ->setSelectEditable($scopes, '- all -');

        $this->addColumn('old_name', 'ERP value')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['old_name'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['old_name']);
                        })                  
                ->setTextEditable();
                        
        $this->addColumn('new_name', 'Shop value')
                ->setRenderer(function($row) {
                            return $row['is_enabled'] ? $row['new_name'] : \Nette\Utils\Html::el('span')->setClass('suppress')->setText($row['new_name']);
                        })
                ->setTextEditable();

        $this->addColumn('is_enabled', 'Enabled?')
                ->setRenderer(function($row) {
                            $value = \Nette\Utils\Html::el('span')
                                    ->setText($row['is_enabled'] ? "yes" : "no");
                    
                            return \Nette\Utils\Html::el('span')
                                    ->setClass(array('icon', $row['is_enabled'] ? "enabled" : "disabled"))
                                    ->setHtml($value);
                        })->setBooleanEditable();
    }
    
    /**
     * Grid init.
     * 
     */
    protected function init() {
        parent::init();

        $self = $this;

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
    }

}