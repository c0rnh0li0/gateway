<?php //netteCache[01]000396a:2:{s:4:"time";s:21:"0.64138400 1390396303";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:74:"/var/www/gateway/app/modules/AdminModule/templates/Setup/editHandler.latte";i:2;i:1365684544;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Setup/editHandler.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'nm3uznwssc')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block tabContent
//
if (!function_exists($_l->blocks['tabContent'][] = '_lbfe5b5e12ad_tabContent')) { function _lbfe5b5e12ad_tabContent($_l, $_args) { extract($_args)
?>    <div class="box form" id="connection-handler">
        <h3>Connection's handler settings update</h3>
        
        <div class="inner">
<?php Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = (is_object("editHandlerForm") ? "editHandlerForm" : $_control["editHandlerForm"]), array()) ;if ($form->hasErrors()): ?>                <ul class="error">
<?php $iterations = 0; foreach ($form->errors as $error): ?>                    <li><?php echo Nette\Templating\Helpers::escapeHtml($error, ENT_NOQUOTES) ?></li>
<?php $iterations++; endforeach ?>
                </ul>
<?php endif ?>
            
                <table>
                    <tr>
                        <th><?php if ($_label = $_form["reader"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                        <td><?php echo $_form["reader"]->getControl()->addAttributes(array()) ?>
 <small><?php echo Nette\Templating\Helpers::escapeHtml($control['editHandlerForm-reader']->getOption('description'), ENT_NOQUOTES) ?></small></td>
                    </tr>
                    <tr<?php if ($_l->tmp = array_filter(array($control['editHandlerForm-writer_settings']->getControl()->isRequired() ? 'required' : ''))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>>
                        <th><?php if ($_label = $_form["reader_settings"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                        <td><?php echo $_form["reader_settings"]->getControl()->addAttributes(array()) ?>
 <small><?php echo Nette\Templating\Helpers::escapeHtml($control['editHandlerForm-reader_settings']->getOption('description'), ENT_NOQUOTES) ?></small></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <th><?php if ($_label = $_form["writer"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                        <td><?php echo $_form["writer"]->getControl()->addAttributes(array()) ?>
 <small><?php echo Nette\Templating\Helpers::escapeHtml($control['editHandlerForm-writer']->getOption('description'), ENT_NOQUOTES) ?></small></td>
                    </tr>
                    <tr<?php if ($_l->tmp = array_filter(array($control['editHandlerForm-writer_settings']->getControl()->isRequired() ? 'required' : ''))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>>
                        <th><?php if ($_label = $_form["writer_settings"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                        <td><?php echo $_form["writer_settings"]->getControl()->addAttributes(array()) ?>
  <small><?php echo Nette\Templating\Helpers::escapeHtml($control['editHandlerForm-writer_settings']->getOption('description'), ENT_NOQUOTES) ?></small></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td><?php echo $_form["reader_id"]->getControl()->addAttributes(array()) ;echo $_form["writer_id"]->getControl()->addAttributes(array()) ;echo $_form["save"]->getControl()->addAttributes(array()) ?>
 <?php echo $_form["cancel"]->getControl()->addAttributes(array()) ?></td>
                    </tr>
                </table>               
<?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ?>
        </div>
    </div>
<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = 'default.latte'; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
// ?>

<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['tabContent']), $_l, get_defined_vars()) ; 