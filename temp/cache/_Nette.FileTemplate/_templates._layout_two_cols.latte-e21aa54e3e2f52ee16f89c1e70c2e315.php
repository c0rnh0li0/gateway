<?php //netteCache[01]000398a:2:{s:4:"time";s:21:"0.31592500 1391985721";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:76:"D:\wamp\www\gateway\app\modules\AdminModule\templates\@layout_two_cols.latte";i:2;i:1390862098;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: D:\wamp\www\gateway\app\modules\AdminModule\templates\@layout_two_cols.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'zqcwp5p26r')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbc5436e9a5b_content')) { function _lbc5436e9a5b_content($_l, $_args) { extract($_args)
?>    <div class="content two-columns <?php if (isset($flip) && $flip > 0): ?>flip<?php endif ?>">
	<?php call_user_func(reset($_l->blocks['beforeTextContent']), $_l, get_defined_vars())  ?>


	<div class="sidebar">
<?php call_user_func(reset($_l->blocks['sidebar']), $_l, get_defined_vars())  ?>
	</div>

	<div class="text-content">
<?php call_user_func(reset($_l->blocks['layoutContent']), $_l, get_defined_vars())  ?>
	</div>

	<div class="clear"></div>
    </div>

    <div class="clear"></div>
<?php
}}

//
// block beforeTextContent
//
if (!function_exists($_l->blocks['beforeTextContent'][] = '_lb45d919de52_beforeTextContent')) { function _lb45d919de52_beforeTextContent($_l, $_args) { extract($_args)
;
}}

//
// block sidebar
//
if (!function_exists($_l->blocks['sidebar'][] = '_lbac629e4bf9_sidebar')) { function _lbac629e4bf9_sidebar($_l, $_args) { extract($_args)
?>		<p class="warning">Block 'sidebar' in Two Columns Layout - must be overriden</p>
<?php
}}

//
// block layoutContent
//
if (!function_exists($_l->blocks['layoutContent'][] = '_lbcc8659ef4b_layoutContent')) { function _lbcc8659ef4b_layoutContent($_l, $_args) { extract($_args)
;Nette\Latte\Macros\UIMacros::callBlock($_l, 'breadcrumbs', $template->getParameters()) ?>

<?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'pageTitle', $template->getParameters()) ;Nette\Latte\Macros\UIMacros::callBlock($_l, 'flashMessages', $template->getParameters()) ?>

<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars()) ; 
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb1a35873841_textContent')) { function _lb1a35873841_textContent($_l, $_args) { extract($_args)
?>		    <p class="warning">Block 'textContent' in Two Columns Layout - must be overriden</p>
<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = '@layout.latte'; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
// ?>

<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 