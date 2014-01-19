<?php

namespace Gateway\DataSource\Entity\Product;
    
/**
 * Simple product entity.
 *
 * @author Lukas Bruha
 */
class Image extends \Gateway\DataSource\Entity implements \Gateway\DataSource\Entity\Product\IImage {

    protected $type = self::TYPE_GALLERY;

    protected $name;
    
    protected $label;
    
    protected $priority = 0;
    
    protected $isVisible = true;
    
    protected $isEnabled = true;
    
    protected $path = null;
    
    public function __construct($name, $type = self::TYPE_GALLERY) {
        $this->name = $name;
        $this->type = $type;
    }
    
    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getLabel() {
        return $this->label ? $this->label : $this->getName();
    }

    public function setLabel($label) {
        $this->label = $label;
    }

    public function isVisible() {
        return $this->isVisible;
    }

    public function setIsVisible($isVisible) {
        $this->isVisible = $isVisible;
    }

    public function isEnabled() {
        return $this->isEnabled;
    }

    public function setIsEnabled($isEnabled) {
        $this->isEnabled = $isEnabled;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function setPriority($priority) {
        $this->priority = $priority;
    }

    public function getPath() {
        return $this->path ? $this->path : $this->getName();
    }

    public function setPath($path) {
        $this->path = $path;
    }
        
    public function __toString() {
        return $this->getName();
    }
    
}

