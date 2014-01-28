<?php //netteCache[01]000397a:2:{s:4:"time";s:21:"0.09384000 1390862381";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:75:"D:\wamp\www\gateway\app\modules\AdminModule\templates\Default\default.latte";i:2;i:1390862098;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: D:\wamp\www\gateway\app\modules\AdminModule\templates\Default\default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'kgx4uoqexd')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lbdcad490be7_pageTitle')) { function _lbdcad490be7_pageTitle($_l, $_args) { extract($_args)
?>    <h2>Overview</h2>
<?php
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lbf5d14fe596_textContent')) { function _lbf5d14fe596_textContent($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['_ajaxChange'][] = '_lbe29eaca0b3__ajaxChange')) { function _lbe29eaca0b3__ajaxChange($_l, $_args) { extract($_args); $_control->validateControl('ajaxChange')
;echo Nette\Templating\Helpers::escapeHtml($maintenanceFile, ENT_NOQUOTES) ;
}}

//
// block sidebar
//
if (!function_exists($_l->blocks['sidebar'][] = '_lbf1074e51d4_sidebar')) { function _lbf1074e51d4_sidebar($_l, $_args) { extract($_args)
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