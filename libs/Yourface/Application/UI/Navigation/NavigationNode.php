<?php

/**
 * Navigation node
 *
 * @author Jan Marek
 * @license MIT
 */

namespace Yourface\Application\UI;

use Nette\ComponentModel\Container;

class NavigationNode extends Container {

    /** @var string */
    public $label;

    /** @var string */
    public $url;

    /** @var bool */
    public $isCurrent = false;

    public $inPath = false;
    
    /**
     * Add navigation node as a child
     * @staticvar int $counter
     * @param string $label
     * @param string $url
     * @return NavigationNode
     */
    public function add($label, $url) {
        $navigationNode = new self;
        
        if (\Nette\Environment::getContext()->hasService("translator")) {
            $label = \Nette\Environment::getContext()->translator->translate($label);
        }
        
        $navigationNode->label = $label;
        $navigationNode->url = $url;

        static $counter;
        $this->addComponent($navigationNode, ++$counter);

        if ($this->isCurrentUrl($url)) {
            $this->setCurrent($navigationNode);

            $navigationNode->getParent()->inPath = true;
        }

        return $navigationNode;
    }

    /**
     * Check current URL to be equal.
     * 
     * @param string $url
     * @return bool
     */
    public function isCurrentUrl($url) {
        $presenter = $this->lookup('\Nette\Application\IPresenter');
        $fully = $presenter->getAction(true);

        return $presenter->link($fully) == $url;
    }

    /**
     * Set node as current
     * @param NavigationNode $node
     */
    public function setCurrent(NavigationNode $node) {
        return $this->parent->setCurrent($node);
    }

}
