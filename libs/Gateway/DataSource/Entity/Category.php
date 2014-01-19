<?php

namespace Gateway\DataSource\Entity;

/**
 * Pure Category wrapper.
 *
 * @author Lukas Bruha
 */
class Category extends \Gateway\DataSource\Entity implements \Gateway\DataSource\IEntity {

    /**
     * Id.
     *
     * @var int
     */
    protected $id;

    /**
     * Country.
     *
     * @var \Gateway\DataSource\Entity\Category
     */
    protected $parent;

    /**
     * Visibility.
     *
     * @var bool
     */
    protected $isVisible = true;

    /**
     * Is active?
     *
     * @var bool
     */
    protected $isActive = true;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Description (not localized).
     *
     * @var string
     */
    protected $description;

    /**
     * Meta keywords (not localized).
     *
     * @var string
     */
    protected $metaKeywords;

    /**
     * Meta description (not localized).
     *
     * @var string
     */
    protected $metaDescription;

    /**
     * Category image/thumbnail.
     *
     * @var mixed
     */
    protected $image;

    /**
     * Localized alternatives.
     *
     * @var \Gateway\DataSource\Entity\Category\LocalizedInfo
     */
    protected $localized = array();

    /**
     * Return property.
     * 
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getName() {
        if (!$this->name && $this->hasLocalizedInfo()) {
            $default = current($this->getLocalizedInfo());

            return $default->name;
        }

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
     * @return \Gateway\DataSource\Entity\Category
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Category
     */
    public function setParent(Category $parent) {
        $this->parent = $parent;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function getIsVisible() {
        return $this->isVisible;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsVisible($isVisible) {
        $this->isVisible = $isVisible;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
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
     * Return property existance.
     * 
     * @return bool
     */
    public function hasParent() {
        return $this->getParent() ? true : false;
    }

    /**
     * Adds localized data
     * 
     * @param \Gateway\DataSource\Entity\Category\LocalizedInfo
     * @return \Gateway\DataSource\Entity\Category
     */
    public function addLocalizedInfo(\Gateway\DataSource\Entity\Category\LocalizedInfo $localized) {
        $this->localized[$localized->lang] = $localized;

        return $this;
    }

    /**
     * Return property - also directly localized one.
     * 
     * @param string $lang
     * @return string
     */
    public function getLocalizedInfo($lang = false) {
        if ($lang) {
            if ($this->hasLocalizedInfo($lang)) {
                return $this->localized[$lang];
            }
        } elseif ($this->hasLocalizedInfo()) {
            return $this->localized;
        }

        return null;
    }

    /**
     * Checks if category has any/specific localized data.
     * 
     * @param string $lang
     * @return bool
     */
    public function hasLocalizedInfo($lang = false) {
        if ($lang) {
            return isset($this->localized[$lang]) ? true : false;
        }

        return count($this->localized) ? true : false;
    }

    /**
     * Returns path.
     * 
     * @param bool $numerical Only IDs in path.
     * @param string $lang
     * @return string
     */
    public function getPath($numerical = false, $lang = false) {
        // tree conversion to string path if requested
        $current = $this;
        $str = array();

        if ($numerical) {
            $str[] = $current->id;
        } else {
            if ($lang && $current->hasLocalizedInfo($lang)) {
                $str[] = $current->getLocalizedInfo($lang)->getName();
            } else {
                $str[] = $current->getName();
            }
        }

        while ($current->hasParent()) {
            $parent = $current->getParent();

            // FIXME do in writer
            // we replace by is_active::is_anchor::include_in_menu
            //$options = sprintf('::%s::%s::%s', $parent->isActive, $parent['is_anchor'], $parent['include_in_menu']);

            if ($numerical) {
                $name = $parent->id;
            } else {
                if ($lang && $parent->hasLocalizedInfo($lang)) {
                    $name = $parent->getLocalizedInfo($lang)->getName();
                } else {
                    $name = $parent->getName();
                }
            }

            //$str[] = $name . $options;
            $str[] = $name;

            $current = $parent;
        }

        return implode("/", array_reverse($str));
    }

    /**
     * Return property.
     * 
     * @return int
     */
    public function getKey() {
        return $this->id;
    }

    /**
     * Return entity path as string.
     * 
     * @return string
     */
    public function __toString() {
        return $this->getPath();
    }

}
