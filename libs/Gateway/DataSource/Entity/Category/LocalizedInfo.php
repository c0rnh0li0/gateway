<?php

namespace Gateway\DataSource\Entity\Category;

/**
 * Localized Category information.
 *
 * @author Lukas Bruha
 */
class LocalizedInfo extends \Gateway\DataSource\Entity\Localized {

    /**
     * Localized name.
     *
     * @var string
     */
    protected $name;

    /**
     * Localized description.
     *
     * @var string
     */
    protected $description;

    /**
     * Localized meta keywords.
     *
     * @var string
     */
    protected $metaKeywords;

    /**
     * Localized meta description.
     *
     * @var string
     */
    protected $metaDescription;

    /**
     * Localized image path or file.
     *
     * @var mixed
     */
    protected $image;

    /**
     * Return property.
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Return property.
     * 
     * @return mixed
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Sets property.
     * 
     * @param mixed
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     * Checks if entity has parent.
     * 
     * @return bool
     */
    public function hasParent() {
        return $this->getParent() ? true : false;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getPath() {
        // tree conversion to string path if requested
        $current = $this;
        $str = array();

        $str[] = $current->name;

        while ($current->hasParent()) {
            $parent = $current->getParent();

            $name = $parent->name;

            //$str[] = $name . $options;
            $str[] = $name;

            $current = $parent;
        }

        return implode("/", array_reverse($str));
    }

    /**
     * Return entity's path as string.
     * 
     * @return string
     */
    public function __toString() {
        return $this->getPath();
    }

}
