<?php //netteCache[01]000386a:2:{s:4:"time";s:21:"0.46823600 1390074995";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:64:"/var/www/gateway/app/modules/AdminModule/templates/@layout.latte";i:2;i:1383647104;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/app/modules/AdminModule/templates/@layout.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '3qta10als5')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block head_meta
//
if (!function_exists($_l->blocks['head_meta'][] = '_lb720a6f91d5_head_meta')) { function _lb720a6f91d5_head_meta($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['head_scripts'][] = '_lb4c6e82e98f_head_scripts')) { function _lb4c6e82e98f_head_scripts($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['head_links'][] = '_lb53a3827906_head_links')) { function _lb53a3827906_head_links($_l, $_args) { extract($_args)
?>            <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/reset.css" />
            <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/site.css" />
            <link rel="stylesheet" type="text/css" media="screen" href="<?php echo htmlSpecialChars($basePath) ?>/css/gateway.css" />

                        
	    <?php call_user_func(reset($_l->blocks['head_links_layout']), $_l, get_defined_vars())  ?>

<?php
}}

//
// block head_links_layout
//
if (!function_exists($_l->blocks['head_links_layout'][] = '_lb6cdb4d0854_head_links_layout')) { function _lb6cdb4d0854_head_links_layout($_l, $_args) { extract($_args)
;
}}

//
// block layout
//
if (!function_exists($_l->blocks['layout'][] = '_lb57b0a94e09_layout')) { function _lb57b0a94e09_layout($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['header'][] = '_lbb2d759cf8f_header')) { function _lbb2d759cf8f_header($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['menu'][] = '_lb7b3fb48cba_menu')) { function _lb7b3fb48cba_menu($_l, $_args) { extract($_args)
?>				    <div id="menu">
<?php $_ctrl = $_control->getComponent("navigation"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
				    </div>
<?php
}}

//
// block beforeLayout
//
if (!function_exists($_l->blocks['beforeLayout'][] = '_lbef3d3ace91_beforeLayout')) { function _lbef3d3ace91_beforeLayout($_l, $_args) { extract($_args)
;
}}

//
// block afterLayout
//
if (!function_exists($_l->blocks['afterLayout'][] = '_lbd01dc0271f_afterLayout')) { function _lbd01dc0271f_afterLayout($_l, $_args) { extract($_args)
;
}}

//
// block footer
//
if (!function_exists($_l->blocks['footer'][] = '_lb436774b84d_footer')) { function _lb436774b84d_footer($_l, $_args) { extract($_args)
?>				<div class="content">
<?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'footerContent', $template->getParameters()) ?>

				    <div class="clear"></div>
				</div>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb8a908bcc8a_content')) { function _lb8a908bcc8a_content($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['layoutContent'][] = '_lbf9b80b40ea_layoutContent')) { function _lbf9b80b40ea_layoutContent($_l, $_args) { extract($_args)
;Nette\Latte\Macros\UIMacros::callBlock($_l, 'pageTitle', $template->getParameters()) ;Nette\Latte\Macros\UIMacros::callBlock($_l, 'flashMessages', $template->getParameters()) ?>

		<div class="clear"></div>

<?php call_user_func(reset($_l->blocks['textContent']), $_l, get_defined_vars()) ; 
}}

//
// block textContent
//
if (!function_exists($_l->blocks['textContent'][] = '_lb3377e97e22_textContent')) { function _lb3377e97e22_textContent($_l, $_args) { extract($_args)
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
if (!function_exists($_l->blocks['pageTitle'][] = '_lb6917484912_pageTitle')) { function _lb6917484912_pageTitle($_l, $_args) { extract($_args)
?>        <h2>Not specified</h2>
<?php
}}

//
// block flashMessages
//
if (!function_exists($_l->blocks['flashMessages'][] = '_lb03e778c362_flashMessages')) { function _lb03e778c362_flashMessages($_l, $_args) { extract($_args)
 ?>
<div id="<?php echo $_control->getSnippetId('flashMessages') ?>"><?php call_user_func(reset($_l->blocks['_flashMessages']), $_l, $template->getParameters()) ?>
</div><?php
}}

//
// block _flashMessages
//
if (!function_exists($_l->blocks['_flashMessages'][] = '_lbf15f1135ed__flashMessages')) { function _lbf15f1135ed__flashMessages($_l, $_args) { extract($_args); $_control->validateControl('flashMessages')
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
if (!function_exists($_l->blocks['footerContent'][] = '_lb5bf3045a58_footerContent')) { function _lb5bf3045a58_footerContent($_l, $_args) { extract($_args)
;
}}

//
// block breadcrumbs
//
if (!function_exists($_l->blocks['breadcrumbs'][] = '_lba2223b98ca_breadcrumbs')) { function _lba2223b98ca_breadcrumbs($_l, $_args) { extract($_args)
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





			    
