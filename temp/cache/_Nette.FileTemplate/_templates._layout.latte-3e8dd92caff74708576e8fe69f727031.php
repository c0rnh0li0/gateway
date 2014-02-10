<?php //netteCache[01]000389a:2:{s:4:"time";s:21:"0.14857400 1391873662";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:67:"D:\wamp\www\gateway\app\modules\AdminModule\templates\@layout.latte";i:2;i:1390862098;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: D:\wamp\www\gateway\app\modules\AdminModule\templates\@layout.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'hjlvdnvien')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block head_meta
//
if (!function_exists($_l->blocks['head_meta'][] = '_lb1ca3422d40_head_meta')) { function _lb1ca3422d40_head_meta($_l, $_args) { extract($_args)
?>	    <meta name="author" content="www.yourface.cz" />
	    <meta name="copyright" content="www.yourface.cz" />

	    <meta http-equiv="content-language" content="en" />
	    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

	    <meta http-equiv="cache-Control" content="must-revalidate, post-check=0, pre-check=0" />
	    <meta http-equiv="pragma" content="public" />

	    <meta http-equiv="cache-control" content="no-cache" />
	    <meta http-equiv="pragma" content="no-cache" />
	    <meta http-equiv="expires" content="-1" />

	    <meta name="robots" content="index,follow" />
	    <meta name="googlebot" content="index,follow,snippet,archive" />
<?php
}}

//
// block head_scripts
//
if (!function_exists($_l->blocks['head_scripts'][] = '_lb83881888bc_head_scripts')) { function _lb83881888bc_head_scripts($_l, $_args) { extract($_args)
?>	    <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/library/jquery-ui/js/jquery-1.7.1.min.js"></script>
	    <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/library/jquery-ui/js/jquery-ui-1.8.18.custom.min.js"></script>
	    <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/jquery.nette.js"></script>
	                            <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/library/nifty-grid/grid.js"></script>
            <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/library/nifty-grid/jquery-ui.js"></script>
            
	    	    <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/yf.ajax.js"></script>
	    <script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/yf.common.js"></script>
		
		<script src="<?php echo htmlSpecialChars($baseUri) ?>/js/jquery.js"></script>
		<script src="<?php echo htmlSpecialChars($baseUri) ?>/js/netteForms.js"></script>		
		<script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/nette.ajax.js"></script>
		<script type="text/javascript" src="<?php echo htmlSpecialChars($baseUri) ?>/js/main.js"></script>
<?php
}}

//
// block head_links
//
if (!function_exists($_l->blocks['head_links'][] = '_lb357563b943_head_links')) { function _lb357563b943_head_links($_l, $_args) { extract($_args)
?>            <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/reset.css" />
            <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/site.css" />
            <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/gateway.css" />

                        
	    <?php call_user_func(reset($_l->blocks['head_links_layout']), $_l, get_defined_vars())  ?>

<?php
}}

//
// block head_links_layout
//
if (!function_exists($_l->blocks['head_links_layout'][] = '_lbf819fb1606_head_links_layout')) { function _lbf819fb1606_head_links_layout($_l, $_args) { extract($_args)
;
}}

//
// block layout
//
if (!function_exists($_l->blocks['layout'][] = '_lbe024948ce1_layout')) { function _lbe024948ce1_layout($_l, $_args) { extract($_args)
?>		<div class="outer-wrapper">

		    <div id="header">

			<div class="inner-wrapper">					
<?php call_user_func(reset($_l->blocks['header']), $_l, get_defined_vars())  ?>
			</div>
                        
                        <div class="clear"></div>
		    </div>
		</div>

		<div class="outer-wrapper">
		    <?php call_user_func(reset($_l->blocks['beforeLayout']), $_l, get_defined_vars())  ?>


		    <div id="layout">
			<div class="inner-wrapper">
<?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'content', $template->getParameters()) ?>
			</div>
		    </div>

		    <?php call_user_func(reset($_l->blocks['afterLayout']), $_l, get_defined_vars())  ?>

		</div>

		<div class="outer-wrapper">
		    <div id="footer">
			<div class="inner-wrapper">
<?php call_user_func(reset($_l->blocks['footer']), $_l, get_defined_vars())  ?>

			    <div id="copyright">
				<p>
				    Copyright &copy; <?php echo Nette\Templating\Helpers::escapeHtml($template->date('0', '%Y'), ENT_NOQUOTES) ?>
 <a href="http://www.yourface.cz" title="<?php echo htmlSpecialChars(_("go to Yourface.cz")) ?>">Yourface.cz</a>. All rights reserved.				    
				</p>
				<p class="version">
				    Version 2.4.1 (last updated on 22.01.2013)
				</p>

				<div class="clear"></div>
			    </div>
			    
			</div>
		    </div>
		</div>

<?php
}}

//
// block header
//
if (!function_exists($_l->blocks['header'][] = '_lbcb05bf7207_header')) { function _lbcb05bf7207_header($_l, $_args) { extract($_args)
?>				<h1 id="logo">
				    <a href="<?php echo htmlSpecialChars($_presenter->link("Default:")) ?>" title="<?php echo htmlSpecialChars(_("return to home")) ?>
"><span><?php echo Nette\Templating\Helpers::escapeHtml(_("NEW Gateway Interface v2.1 BE"), ENT_NOQUOTES) ?></span></a>
				</h1>					
<?php call_user_func(reset($_l->blocks['menu']), $_l, get_defined_vars())  ?>

				<div class="clear"></div>
<?php
}}

//
// block menu
//
if (!function_exists($_l->blocks['menu'][] = '_lbf10944b8cf_menu')) { function _lbf10944b8cf_menu($_l, $_args) { extract($_args)
?>				    <div id="menu">
<?php $_ctrl = $_control->getComponent("navigation"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
				    </div>
<?php
}}

//
// block beforeLayout
//
if (!function_exists($_l->blocks['beforeLayout'][] = '_lb3137535001_beforeLayout')) { function _lb3137535001_beforeLayout($_l, $_args) { extract($_args)
;
}}

//
// block afterLayout
//
if (!function_exists($_l->blocks['afterLayout'][] = '_lb50abb0acc7_afterLayout')) { function _lb50abb0acc7_afterLayout($_l, $_args) { extract($_args)
;
}}

//
// block footer
//
if (!function_exists($_l->blocks['footer'][] = '_lbe83d5dcf8b_footer')) { function _lbe83d5dcf8b_footer($_l, $_args) { extract($_args)
?>				<div class="content">
<?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'footerContent', $template->getParameters()) ?>

				    <div class="clear"></div>
				</div>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb9ebe468a88_content')) { function _lb9ebe468a88_content($_l, $_args) { extract($_args)
?>   <div class="content simple">
        <div class="text-content">
<?php call_user_func(reset($_l->blocks['layoutContent']), $_l, get_defined_vars())  ?>
        </div>
    </div>

    <div class="clear"></div>
<?php
}}

//
// block layoutContent
//
if (!function_exists($_l->blocks['layoutContent'][] = '_lb7dafc3e447_layoutContent')) { function _lb7dafc3e447_layoutContent($_l, $_args) { extract($_args)
;Nette\Latte\Macros\UIMacros::callBlock($_l, 'pageTitle', $template->getParameters()) ;Nette\Latte\Macros\UIMacros::callBlock($_l, 'flashMessages', $template->getParameters()) ?>

		<div class="clear"></div>

<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars()) ; 
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lbc374bfcbd5_textContent')) { function _lbc374bfcbd5_textContent($_l, $_args) { extract($_args)
?>                    <p class="warning">Please, define #textContent block.</p>

		    <p>
			<b>Loaded templates</b><br />
			<small>layout: <code><?php echo Nette\Templating\Helpers::escapeHtml($template->replace($template->getFile(), $root), ENT_NOQUOTES) ?></code></small><br />
			<small>view: <code><?php echo Nette\Templating\Helpers::escapeHtml($template->replace($presenter->template->getFile(), $root), ENT_NOQUOTES) ?></code></small>
		    </p>

<?php
}}

//
// block pageTitle
//
if (!function_exists($_l->blocks['pageTitle'][] = '_lb7864215880_pageTitle')) { function _lb7864215880_pageTitle($_l, $_args) { extract($_args)
?>        <h2>Not specified</h2>
<?php
}}

//
// block flashMessages
//
if (!function_exists($_l->blocks['flashMessages'][] = '_lb589786228b_flashMessages')) { function _lb589786228b_flashMessages($_l, $_args) { extract($_args)
 ?>
<div id="<?php echo $_control->getSnippetId('flashMessages') ?>"><?php call_user_func(reset($_l->blocks['_flashMessages']), $_l, $template->getParameters()) ?>
</div><?php
}}

//
// block _flashMessages
//
if (!function_exists($_l->blocks['_flashMessages'][] = '_lb04941ad368__flashMessages')) { function _lb04941ad368__flashMessages($_l, $_args) { extract($_args); $_control->validateControl('flashMessages')
;if ((count($flashes))): ?>
	    <div class="flash-messages">
<?php $iterations = 0; foreach ($flashes as $flash): ?>
		    <p class="item <?php echo htmlSpecialChars($flash->type) ?>"><?php echo $flash->message ?></p>
<?php $iterations++; endforeach ?>
	    </div>
<?php endif ;
}}

//
// block footerContent
//
if (!function_exists($_l->blocks['footerContent'][] = '_lbb44def7459_footerContent')) { function _lbb44def7459_footerContent($_l, $_args) { extract($_args)
;
}}

//
// block breadcrumbs
//
if (!function_exists($_l->blocks['breadcrumbs'][] = '_lbb3da77c6ec_breadcrumbs')) { function _lbb3da77c6ec_breadcrumbs($_l, $_args) { extract($_args)
;if ($presenter->getName() != 'Admin:Default'): ?>
        <div id="breadcrumbs">            
<?php $_ctrl = $_control->getComponent("navigation"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->renderBreadcrumbs() ?>
        </div>
<?php endif ;
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
?>
<!DOCTYPE html>
<html lang="en">
    <head>
	<title>NEW Gateway Interface v2.1 - Back-End</title>
	
<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['head_meta']), $_l, get_defined_vars())  ?>
        
<?php call_user_func(reset($_l->blocks['head_scripts']), $_l, get_defined_vars())  ?>
        
<?php call_user_func(reset($_l->blocks['head_links']), $_l, get_defined_vars())  ?>
        
    </head>

    <body>
	<div id="container">
<?php call_user_func(reset($_l->blocks['layout']), $_l, get_defined_vars())  ?>
	</div>
    
    </body>
</html>





			    
