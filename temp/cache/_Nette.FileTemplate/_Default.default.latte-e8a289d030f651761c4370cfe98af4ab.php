<?php //netteCache[01]000394a:2:{s:4:"time";s:21:"0.99310400 1390075070";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:72:"/var/www/gateway/app/modules/AdminModule/templates/Default/default.latte";i:2;i:1383647104;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Default/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '3kebfd6q33')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lb3fcd8e0b13_pageTitle')) { function _lb3fcd8e0b13_pageTitle($_l, $_args) { extract($_args)
?>    <h2>Overview</h2>
<?php
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb4bd9462286_textContent')) { function _lb4bd9462286_textContent($_l, $_args) { extract($_args)
?>	<div id="content">
		<a class="ajax" href="<?php echo htmlSpecialChars($_control->link("changeVariable!")) ?>
"><div id="<?php echo $_control->getSnippetId('ajaxChange') ?>"><?php call_user_func(reset($_l->blocks['_ajaxChange']), $_l, $template->getParameters()) ?>
</div></a>
	</div>

    <div class="box">
        <h3>Currently running schedules</h3>
        
<?php $_ctrl = $_control->getComponent("grid"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    </div>
    
    <div class="box" id="global-logs">
        <h3>Global logs <small>(application's logs, not older than 2 days)</small></h3>
        
<?php $_ctrl = $_control->getComponent("logsGrid"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    </div>
    
    <div class="box" id="connection-logs">
        <h3>Schedule logs <small>(currently running or cancelled, not older than 2 days)</small></h3>
        
<?php $_ctrl = $_control->getComponent("connectionLogsGrid"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
    </div>    
    
    
    <div class="clear"></div>
<?php
}}

//
// block _ajaxChange
//
if (!function_exists($_l->blocks['_ajaxChange'][] = '_lbb02e8d0b37__ajaxChange')) { function _lbb02e8d0b37__ajaxChange($_l, $_args) { extract($_args); $_control->validateControl('ajaxChange')
;echo Nette\Templating\Helpers::escapeHtml($maintenanceFile, ENT_NOQUOTES) ;
}}

//
// block sidebar
//
if (!function_exists($_l->blocks['sidebar'][] = '_lbb72eab3fed_sidebar')) { function _lbb72eab3fed_sidebar($_l, $_args) { extract($_args)
?>    <div class="box form">
        <h3>Server limits</h3>
        
        <div class="inner">
            <dl>
                <dt>upload_max_filesize</dt>
                <dd><?php echo Nette\Templating\Helpers::escapeHtml(ini_get("upload_max_filesize"), ENT_NOQUOTES) ?></dd>
                <dt>post_max_size</dt>
                <dd><?php echo Nette\Templating\Helpers::escapeHtml(ini_get("post_max_size"), ENT_NOQUOTES) ?></dd>
                <dt>max_execution_time</dt>
                <dd><?php echo Nette\Templating\Helpers::escapeHtml(ini_get("max_execution_time"), ENT_NOQUOTES) ?></dd>
                <dt>max_input_time</dt>
                <dd><?php echo Nette\Templating\Helpers::escapeHtml(ini_get("max_input_time"), ENT_NOQUOTES) ?></dd>
                <dt>memory_limit</dt>
                <dd><?php echo Nette\Templating\Helpers::escapeHtml(ini_get("memory_limit"), ENT_NOQUOTES) ?></dd>
            </dl>
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