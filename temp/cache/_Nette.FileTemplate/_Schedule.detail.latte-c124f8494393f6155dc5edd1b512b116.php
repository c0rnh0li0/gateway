<?php //netteCache[01]000394a:2:{s:4:"time";s:21:"0.69059000 1390492347";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:72:"/var/www/gateway/app/modules/AdminModule/templates/Schedule/detail.latte";i:2;i:1365684544;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/Schedule/detail.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '7nxhoylrkp')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lbb2fc35eca6_pageTitle')) { function _lbb2fc35eca6_pageTitle($_l, $_args) { extract($_args)
?>    <h2>Schedule report</h2>
<?php
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb13ffbad408_textContent')) { function _lb13ffbad408_textContent($_l, $_args) { extract($_args)
?>    <div class="box" id="schedule-report">
        <dl>
            <dt>Schedule ID:</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($report->id, ENT_NOQUOTES) ?></dd>
            <dt>Connection:</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($report->name, ENT_NOQUOTES) ?></dd>
            <dt>Inserted at:</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($report->inserted_at, ENT_NOQUOTES) ?></dd>
            <dt>Executed at:</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($report->executed_at, ENT_NOQUOTES) ?></dd>
            <dt>Completed at:</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($report->finished_at ? $report->finished_at : 'running', ENT_NOQUOTES) ?></dd>
            <dt>Processed in:</dt>
            <dd><?php echo Nette\Templating\Helpers::escapeHtml($report->finished_at ? $processLength : 0, ENT_NOQUOTES) ?></dd>
            <dt>Input source:</dt>
            <dd><a href="<?php echo htmlSpecialChars($_control->link("source", array($report->gw_schedule_id))) ?>
">view source</a></dd>
        </dl>
        
        <div class="clear"></div>
    </div>

    <div class="box">
        <h3>Complete log</h3>
        
<?php if (!$logFile): ?>
            <p class="error">Log file was not found in <?php echo Nette\Templating\Helpers::escapeHtml($report->log, ENT_NOQUOTES) ?>.</p>
<?php else: ?>
            <pre><?php echo Nette\Templating\Helpers::escapeHtml($logFile, ENT_NOQUOTES) ?></pre>
<?php endif ?>
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

<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars()) ; 