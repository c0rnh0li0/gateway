<?php //netteCache[01]000379a:2:{s:4:"time";s:21:"0.74504200 1390074995";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:57:"/var/www/gateway/libs/NiftyGrid/templates/paginator.latte";i:2;i:1365685600;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"2f3808e released on 2012-07-30";}}}?><?php

// source file: /var/www/gateway/libs/NiftyGrid/templates/paginator.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'xxk516buea')
;
// prolog Nette\Latte\Macros\UIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($paginator->pageCount > 1): ?>
<div class="grid-paginator">
<?php $iterations = 0; foreach (range($paginator->getBase(), $paginator->getPageCount()) as $page): $iterations++; endforeach ;if (!$paginator->isFirst()): ?>
		<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->getFirstPage()))) ?>" class="grid-ajax">&laquo;</a>
<?php else: ?>
                <span>&laquo;</span>
<?php endif ;if ($paginator->getPage() - 1 >= $paginator->getFirstPage()): ?>
		<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->getPage() - 1))) ?>" class="grid-ajax">&lsaquo;</a>
<?php else: ?>
                <span>&lsaquo;</span>
<?php endif ?>
	<span class="grid-current" data-lastpage="<?php echo htmlSpecialChars($paginator->getLastPage()) ?>
"><?php echo Nette\Templating\Helpers::escapeHtml($paginator->getPage(), ENT_NOQUOTES) ?>
 / <?php echo Nette\Templating\Helpers::escapeHtml($paginator->getLastPage(), ENT_NOQUOTES) ?></span>
<?php if ($paginator->getPage() + 1 <= $paginator->getLastPage()): ?>
		<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->getPage() + 1))) ?>" class="grid-ajax">&rsaquo;</a>
<?php else: ?>
                <span>&rsaquo;</span>
<?php endif ;if (!$paginator->isLast()): ?>
		<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->getLastPage()))) ?>" class="grid-ajax">&raquo;</a>
<?php else: ?>
                <span>&raquo;</span>
<?php endif ?>
</div>
<?php endif ;