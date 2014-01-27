<?php //netteCache[01]000392a:2:{s:4:"time";s:21:"0.12189900 1390396295";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:70:"/var/www/gateway/app/modules/AdminModule/templates/Setup/default.latte";i:2;i:1390055307;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Setup/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'qnoknb5pgm')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lb314e28cf23_pageTitle')) { function _lb314e28cf23_pageTitle($_l, $_args) { extract($_args)
?>    <h2>Connection '<?php echo Nette\Templating\Helpers::escapeHtml($connection->name, ENT_NOQUOTES) ?>' setup</h2>
<?php
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb9b2eb50f44_textContent')) { function _lb9b2eb50f44_textContent($_l, $_args) { extract($_args)
?>    <div class="box" id="connection-info">
        <span class="icon connection <?php if ($connection->is_enabled): ?>enabled<?php endif ?>
" title="<?php echo htmlSpecialChars(($connection->is_enabled ? 'enabled' : 'disabled')) ?>"></span>
        
        <dl>
            <dt>Description</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($connection->description, ENT_NOQUOTES) ?></dd>
            <dt>Status</dt>
            <dd class="status"><?php if ($connection->is_enabled): ?>

                    <span class="enabled"><span>enabled</span></span>
<?php else: ?>
                    <span class="disabled"><span>disabled</span></span>
<?php endif ?>
            </dd>
        </dl>

        <div class="box">
            <h3>Settings</h3>

            <ul>
                <li<?php if ($_l->tmp = array_filter(array($presenter->action == 'edit' ? 'active':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>
><a href="<?php echo htmlSpecialChars($_control->link("edit")) ?>">Base info</a></li>
                <li<?php if ($_l->tmp = array_filter(array($presenter->action == 'default' ? 'active':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>
><a href="<?php echo htmlSpecialChars($_control->link("default")) ?>">Handlers</a></li>
            </ul>
        </div>

        <div class="box">
            <h3>Mapping</h3>

            <ul>
                <li<?php if ($_l->tmp = array_filter(array($presenter->action == 'attributes' ? 'active':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>
><a href="<?php echo htmlSpecialChars($_control->link("attributes")) ?>">Product attributes</a></li>
                <li<?php if ($_l->tmp = array_filter(array($presenter->action == 'localization' ? 'active':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>
><a href="<?php echo htmlSpecialChars($_control->link("localization")) ?>">Localization</a></li>
                <li<?php if ($_l->tmp = array_filter(array($presenter->action == 'enumeration' ? 'active':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>
><a href="<?php echo htmlSpecialChars($_control->link("enumeration")) ?>">Properties</a></li>
            </ul>
        </div>
    </div>

        
    <div id="connection-content">
<?php call_user_func(reset($_l->blocks['tabContent']), $_l, get_defined_vars())  ?>
        
        <div class="clear"></div>
    </div>
    
    <div>
    	we will be testing here
    </div>
<?php
}}

//
// block tabContent
//
if (!function_exists($_l->blocks['tabContent'][] = '_lbce9d31c4ae_tabContent')) { function _lbce9d31c4ae_tabContent($_l, $_args) { extract($_args)
?>            <div class="box" id="connection-handlers">
                <h3>Defined handlers list</h3>
                
<?php $_ctrl = $_control->getComponent("connectionHandlersGrid"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
            </div>

            <div class="box form"  id="connection-handler">
                <h3>Add new handler</h3>
                
                    <div class="inner">
<?php Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = (is_object("connectionHandlersForm") ? "connectionHandlersForm" : $_control["connectionHandlersForm"]), array()) ;if ($form->hasErrors()): ?>                            <ul class="error">
<?php $iterations = 0; foreach ($form->errors as $error): ?>                                <li><?php echo Nette\Templating\Helpers::escapeHtml($error, ENT_NOQUOTES) ?></li>
<?php $iterations++; endforeach ?>
                            </ul>
<?php endif ?>
                        
                            <table>
                                <tr<?php if ($_l->tmp = array_filter(array($control['connectionHandlersForm-type']->getControl()->isRequired() ? 'required':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>>
                                    <th>
<?php if ($_label = $_form["type"]->getLabel()) echo $_label->addAttributes(array()) ?>
                                    </th>
                                    <td>
<div id="<?php echo $_control->getSnippetId('connectionHandlersFormType') ?>"><?php call_user_func(reset($_l->blocks['_connectionHandlersFormType']), $_l, $template->getParameters()) ?>
</div>                                    </td>
                                </tr>
                                <tr<?php if ($_l->tmp = array_filter(array($control['connectionHandlersForm-reader']->getControl()->isRequired() ? 'required':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>>
                                    <th>
<?php if ($_label = $_form["reader"]->getLabel()) echo $_label->addAttributes(array()) ?>
                                    </th>
                                     <td>
<div id="<?php echo $_control->getSnippetId('connectionHandlersFormReader') ?>"><?php call_user_func(reset($_l->blocks['_connectionHandlersFormReader']), $_l, $template->getParameters()) ?>
</div>                                    </td>
                                </tr>
                                <tr<?php if ($_l->tmp = array_filter(array($control['connectionHandlersForm-writer']->getControl()->isRequired() ? 'required':null))) echo ' class="' . htmlSpecialChars(implode(" ", array_unique($_l->tmp))) . '"' ?>>
                                    <th>
<?php if ($_label = $_form["writer"]->getLabel()) echo $_label->addAttributes(array()) ?>
                                    </th>
                                    <td>
<div id="<?php echo $_control->getSnippetId('connectionHandlersFormWriter') ?>"><?php call_user_func(reset($_l->blocks['_connectionHandlersFormWriter']), $_l, $template->getParameters()) ?>
</div>                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <?php echo $_form["save"]->getControl()->addAttributes(array()) ?>

                                    </td>
                                </tr>
                            </table>
<?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ?>
                                        
                    </div>            

                    <script type="text/javascript">
                        $("form").delegate(<?php echo Nette\Templating\Helpers::escapeJs(('#' . $control["connectionHandlersForm-type"]->htmlId)) ?>, 'change', function() {
                            $.get(<?php echo Nette\Templating\Helpers::escapeJs($control->link('connectionHandlersFormReaders!')) ?>, {"type": $(this).val()});        
                        });
                        
                        $("form").delegate(<?php echo Nette\Templating\Helpers::escapeJs(('#' . $control["connectionHandlersForm-reader"]->htmlId)) ?>, 'change', function() {
                            $.get(<?php echo Nette\Templating\Helpers::escapeJs($control->link('connectionHandlersFormWriters!')) ?>, {"type": $('#frmconnectionHandlersForm-type').val(), "reader": $(this).val()});        
                        });

                    </script>
            </div>                  
<?php
}}

//
// block _connectionHandlersFormType
//
if (!function_exists($_l->blocks['_connectionHandlersFormType'][] = '_lbb847c772fe__connectionHandlersFormType')) { function _lbb847c772fe__connectionHandlersFormType($_l, $_args) { extract($_args); $_control->validateControl('connectionHandlersFormType')
?>                                            <?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-type']->getControl(), ENT_NOQUOTES) ?>

<?php
}}

//
// block _connectionHandlersFormReader
//
if (!function_exists($_l->blocks['_connectionHandlersFormReader'][] = '_lb1d79fd11e4__connectionHandlersFormReader')) { function _lb1d79fd11e4__connectionHandlersFormReader($_l, $_args) { extract($_args); $_control->validateControl('connectionHandlersFormReader')
?>                                            <?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-reader']->getControl(), ENT_NOQUOTES) ?>


                                            <span class="label"><?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-reader_settings']->getLabel(), ENT_NOQUOTES) ?></span>
                                            <?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-reader_settings']->getControl(), ENT_NOQUOTES) ?>

<?php
}}

//
// block _connectionHandlersFormWriter
//
if (!function_exists($_l->blocks['_connectionHandlersFormWriter'][] = '_lb2323bc134a__connectionHandlersFormWriter')) { function _lb2323bc134a__connectionHandlersFormWriter($_l, $_args) { extract($_args); $_control->validateControl('connectionHandlersFormWriter')
?>                                            <?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-writer']->getControl(), ENT_NOQUOTES) ?>


                                        <span class="label"><?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-writer_settings']->getLabel(), ENT_NOQUOTES) ?></span>
                                            <?php echo Nette\Templating\Helpers::escapeHtml($control['connectionHandlersForm-writer_settings']->getControl(), ENT_NOQUOTES) ?>

<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = '../@layout_simple.latte'; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
// ?>

<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['pageTitle']), $_l, get_defined_vars())  ?>

<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars()) ; 