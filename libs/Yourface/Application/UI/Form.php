<?php

namespace Yourface\Application\UI;

use Nette\Utils\Html;

/**
 * Form with DL renderer.
 *
 * @author Lukas Bruha
 */
class Form extends \Nette\Application\UI\Form {

    public function render() {
        // renderer
        $renderer = $this->getRenderer();
        $renderer->wrappers['form']['container'] = Html::el('div')->id('form');
        $renderer->wrappers['form']['errors'] = FALSE;
        $renderer->wrappers['group']['container'] = Html::el('div')->class('group');
        $renderer->wrappers['group']['label'] = 'h3';
        $renderer->wrappers['pair']['container'] = Html::el('dl')->class('pair');
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['control']['container'] = 'dd';
        $renderer->wrappers['control']['.odd'] = 'odd';
        $renderer->wrappers['control']['errors'] = TRUE;
        $renderer->wrappers['label']['container'] = 'dt';
        $renderer->wrappers['label']['suffix'] = '';
        $renderer->wrappers['control']['requiredsuffix'] = " *";

        parent::render();
    }
}

