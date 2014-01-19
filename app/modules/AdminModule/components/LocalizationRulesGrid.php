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
class LocalizationRulesGrid extends \AdminModule\Component\MappingRulesGrid {

    /**
     * Init columns for grid.
     * 
     */
    protected function beforeInit() {
        $this->addColumn('old_name', 'ERP language code')->setTextEditable();
        $this->addColumn('new_name', 'Shop language code')->setTextEditable();

        $this->addColumn('is_enabled', 'Enabled?')
                ->setRenderer(function($row) {
                            $value = \Nette\Utils\Html::el('span')
                                    ->setText($row['is_enabled'] ? "yes" : "no");
                    
                            return \Nette\Utils\Html::el('span')
                                    ->setClass(array('icon', $row['is_enabled'] ? "enabled" : "disabled"))
                                    ->setHtml($value);
                        })->setBooleanEditable();
    }

}