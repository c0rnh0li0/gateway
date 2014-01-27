<?php //netteCache[01]000395a:2:{s:4:"time";s:21:"0.51986000 1390396327";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:73:"/var/www/gateway/app/modules/AdminModule/templates/Schedule/default.latte";i:2;i:1365684544;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Schedule/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'xzin5ao6t8')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lb49d97eebd7_pageTitle')) { function _lb49d97eebd7_pageTitle($_l, $_args) { extract($_args)
?>    <h2>Planned transfers overview</h2>
<?php
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb6e5f88afc5_textContent')) { function _lb6e5f88afc5_textContent($_l, $_args) { extract($_args)
?>    <div class="box">
<?php $_ctrl = $_control->getComponent("scheduleGrid"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    </div>
    
    <div class="box form" id="connection-handler">
        <h3>POST a source via REST API</h3>
        
        <div class="inner">
<?php Nette\Latte\Macros\FormMacros::renderFormBegin($form = $_form = (is_object("sourceForm") ? "sourceForm" : $_control["sourceForm"]), array()) ;if ($form->hasErrors()): ?>               <ul class="error">
<?php $iterations = 0; foreach ($form->errors as $error): ?>                   <li><?php echo Nette\Templating\Helpers::escapeHtml($error, ENT_NOQUOTES) ?></li>
<?php $iterations++; endforeach ?>
               </ul>
<?php endif ?>

               <table>
                   <tr>
                       <th><?php if ($_label = $_form["connection"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                       <td><?php echo $_form["connection"]->getControl()->addAttributes(array()) ?></td>
                   </tr>
                   <tr>
                       <th><?php if ($_label = $_form["handler"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                       <td>
<div id="<?php echo $_control->getSnippetId('sourceFormHandler') ?>"><?php call_user_func(reset($_l->blocks['_sourceFormHandler']), $_l, $template->getParameters()) ?>
</div>                       </td>
                   </tr>          
                       <tr>
                           <th><?php if ($_label = $_form["file"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                           <td>
                               <?php echo $_form["file"]->getControl()->addAttributes(array()) ?><br />
                               <small>Upload file with source...</small> 
                           </td>
                       </tr>          
                       <tr>
                           <th><?php if ($_label = $_form["text"]->getLabel()) echo $_label->addAttributes(array()) ?></th>
                           <td>
                               <?php echo $_form["text"]->getControl()->addAttributes(array()) ?><br />
                               <small>...or set source as text.</small> 
                           </td>
                       </tr>
                   <tr>
                       <th></th>
                       <td>
                           <?php echo $_form["process"]->getControl()->addAttributes(array()) ?>

                       </td>
                   </tr>                    
               </table>
<?php Nette\Latte\Macros\FormMacros::renderFormEnd($_form) ?>
        </div>
        
        <script type="text/javascript">
            $("form").delegate(<?php echo Nette\Templating\Helpers::escapeJs(('#' . $control["sourceForm-connection"]->htmlId)) ?>, 'change', function() {
                $.get(<?php echo Nette\Templating\Helpers::escapeJs($control->link('sourceFormHandlers!')) ?>, {"connName": $(this).val()});
            });
            
            /*
            setInterval(function() {
                $.get(<?php echo Nette\Templating\Helpers::escapeJs($presenter->link('this')) ?>);
            }, 10000);*/
        </script>
    </div>
<?php
}}

//
// block _sourceFormHandler
//
if (!function_exists($_l->blocks['_sourceFormHandler'][] = '_lbaa03a87515__sourceFormHandler')) { function _lbaa03a87515__sourceFormHandler($_l, $_args) { extract($_args); $_control->validateControl('sourceFormHandler')
?>                                                              <?php echo Nette\Templating\Helpers::escapeHtml($control['sourceForm-handler']->getControl(), ENT_NOQUOTES) ?>

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