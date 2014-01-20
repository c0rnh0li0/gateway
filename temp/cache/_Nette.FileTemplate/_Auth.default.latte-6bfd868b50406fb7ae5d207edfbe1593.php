<?php //netteCache[01]000391a:2:{s:4:"time";s:21:"0.54490500 1390232879";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:69:"/var/www/gateway/app/modules/AdminModule/templates/Auth/default.latte";i:2;i:1365684958;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Auth/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'btx18oy42j')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lb926b1b996a_pageTitle')) { function _lb926b1b996a_pageTitle($_l, $_args) { extract($_args)
?>    <h2>NEW GateWay 2.1 authentication</h2>
<?php
}}

//
// block head_links_layout
//
if (!function_exists($_l->blocks['head_links_layout'][] = '_lb6506e34908_head_links_layout')) { function _lb6506e34908_head_links_layout($_l, $_args) { extract($_args)
?>    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/auth.css" />
<?php
}}

//
// block user
//
if (!function_exists($_l->blocks['user'][] = '_lbf50d5013ae_user')) { function _lbf50d5013ae_user($_l, $_args) { extract($_args)
;
}}

//
// block menu
//
if (!function_exists($_l->blocks['menu'][] = '_lb8b7a7804bf_menu')) { function _lb8b7a7804bf_menu($_l, $_args) { extract($_args)
;
}}

//
// block breadcrumbs
//
if (!function_exists($_l->blocks['breadcrumbs'][] = '_lbb118b3f00a_breadcrumbs')) { function _lbb118b3f00a_breadcrumbs($_l, $_args) { extract($_args)
;
}}

//
// block header
//
if (!function_exists($_l->blocks['header'][] = '_lb364bd6dab7_header')) { function _lb364bd6dab7_header($_l, $_args) { extract($_args)
;
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb8639857e6c_textContent')) { function _lb8639857e6c_textContent($_l, $_args) { extract($_args)
?>    <div id="security-login" class="form">
<?php $_ctrl = $_control->getComponent("loginForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    </div>
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

<?php call_user_func(reset($_l->blocks['head_links_layout']), $_l, get_defined_vars())  ?>
            
<?php call_user_func(reset($_l->blocks['user']), $_l, get_defined_vars())  ?>

<?php call_user_func(reset($_l->blocks['menu']), $_l, get_defined_vars())  ?>

<?php call_user_func(reset($_l->blocks['breadcrumbs']), $_l, get_defined_vars())  ?>

<?php call_user_func(reset($_l->blocks['header']), $_l, get_defined_vars())  ?>


<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars()) ; 