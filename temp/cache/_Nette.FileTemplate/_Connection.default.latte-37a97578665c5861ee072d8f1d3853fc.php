<?php //netteCache[01]000397a:2:{s:4:"time";s:21:"0.28224600 1390396291";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:75:"/var/www/gateway/app/modules/AdminModule/templates/Connection/default.latte";i:2;i:1365684544;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Connection/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'pafy90mx9h')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lb237031deda_pageTitle')) { function _lb237031deda_pageTitle($_l, $_args) { extract($_args)
?>    <h2>Connections list</h2>
<?php
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lbdb724689f2_textContent')) { function _lbdb724689f2_textContent($_l, $_args) { extract($_args)
;$_ctrl = $_control->getComponent("connectionsGrid"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    
<?php
}}

//
// block sidebar
//
if (!function_exists($_l->blocks['sidebar'][] = '_lbd70834a065_sidebar')) { function _lbd70834a065_sidebar($_l, $_args) { extract($_args)
?>    <div class="box form" id="connection-new">
        <h3>New connection</h3>
        
        <div class="inner">
<?php $_ctrl = $_control->getComponent("connectionForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
        </div>
    </div>
<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = '../@layout_two_cols.latte'; $template->_extended = $_extended = TRUE;


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

<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars())  ?>


<?php call_user_func(reset($_l->blocks['sidebar']), $_l, get_defined_vars()) ; 