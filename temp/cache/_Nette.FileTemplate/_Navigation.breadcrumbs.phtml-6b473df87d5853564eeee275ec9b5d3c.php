<?php //netteCache[01]000396a:2:{s:4:"time";s:21:"0.50471600 1390074995";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:74:"/var/www/gateway/libs/Yourface/Application/UI/Navigation/breadcrumbs.phtml";i:2;i:1365686004;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/libs/Yourface/Application/UI/Navigation/breadcrumbs.phtml

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'oozggiio96')
;
// prolog Nette\Latte\Macros\UIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
?>
<div class="control breadcrumbs">
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new Nette\Iterators\CachingIterator($items) as $item): if ($iterator->isLast()): ?>
			<span class="current"><?php echo Nette\Templating\Helpers::escapeHtml($item->label, ENT_NOQUOTES) ?></span>
<?php else: ?>
			<a href="<?php echo htmlSpecialChars($item->url) ?>"><?php echo Nette\Templating\Helpers::escapeHtml($item->label, ENT_NOQUOTES) ?></a> &rarr;
<?php endif ;$iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
</div>
