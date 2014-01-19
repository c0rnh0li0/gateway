<?php

namespace Gateway\DataSource\Entity\Product;

use Gateway\DataSource\Entity\Localized;

/**
 * Product description wrapper.
 *
 * @author Lukas Bruha
 */
class Description extends Localized {
    
    protected $name;
    
    protected $description;
    
    protected $shortDescription;

    protected $metaTitle;
    
    protected $metaKeywords;
    
    protected $metaDescription;
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = trim($name);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getShortDescription() {
        return $this->shortDescription;
    }

    public function setShortDescription($shortDescription) {
        $this->shortDescription = $shortDescription;
    }

    public function getMetaTitle() {
        return $this->metaTitle;
    }

    public function setMetaTitle($metaTitle) {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaKeywords() {
        return $this->metaKeywords;
    }

    public function setMetaKeywords($metaKeywords) {
        if (is_array($metaKeywords)) {
            $metaKeywords = implode(",", $metaKeywords);
        }
        
        $this->metaKeywords = $metaKeywords;
    }

    public function getMetaDescription() {
        return $this->metaDescription;
    }

    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;
    }


    
}
