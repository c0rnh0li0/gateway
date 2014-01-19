<?php

namespace Gateway\DataSource\Entity;

use Gateway\DataSource\IEntity;

/**
 * Base product entity.
 *
 * @author Lukas Bruha
 */
abstract class Product extends \Gateway\DataSource\Entity implements IProduct, IEntity {

    /**
     * ID.
     *
     * @var int
     */
    protected $id;

    /**
     * Parent product.
     *
     * @var \Gateway\DataSource\Entity\Product
     */
    protected $parent = null;

    /**
     * Product unique SKU.
     *
     * @var string
     */
    protected $sku;

    /**
     * Type of product.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Price.
     *
     * @var float
     */
    protected $price;

    /**
     * Does price include tax?
     *
     * @var bool
     */
    protected $isPriceInclTax = true;

    /**
     * Tax.
     *
     * @var float
     */
    protected $tax;

    /**
     * Tax class ID.
     *
     * @var float
     */	
	protected $taxClassId;

    /**
     * Discount.
     *
     * @var float
     */
    protected $discount;

    /**
     * Quantity.
     *
     * @var int
     */
    protected $quantity;

    /**
     * Visibility.
     *
     * @var bool
     */
    protected $isVisible;

    /**
     * Weight.
     *
     * @var float
     */
    protected $weight;

    /**
     * Is enabled?
     *
     * @var true
     */
    protected $isEnabled;

    /**
     * Manufacturer.
     *
     * @var string
     */
    protected $manufacturer;

    /**
     * Product images.
     *
     * @var array
     */
    protected $images = array();

    /**
     * Localized descriptions.
     *
     * @var array
     */
    protected $descriptions = array();

    /**
     * Localized categories.
     *
     * @var array
     */
    protected $categories = array();

    /**
     * Localized attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Quantity incl. price.
     *
     * @var float
     */
    // should be $quantity * $price
    protected $finalPrice = null;

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
     * @return \Gateway\DataSource\Entity\Product
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets property.
     * 
     * @param \Gateway\DataSource\Entity\Product
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setSku($sku) {
        $this->sku = $sku;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Return property.
     * 
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Sets property.
     * 
     * @param float
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * Return property.
     * 
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Sets property.
     * 
     * @param int
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
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
        if ($isVisible instanceof \Gateway\DataSource\Entity\Product\Visibility) {
            $this->isVisible = $isVisible;
        } else {
            $this->isVisible = new \Gateway\DataSource\Entity\Product\Visibility($isVisible);
        }
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getWeight() {
        return $this->weight;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setWeight($weight) {
        $this->weight = $weight;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function getIsEnabled() {
        return $this->isEnabled;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsEnabled($isEnabled) {
        $this->isEnabled = $isEnabled;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getManufacturer() {
        return $this->manufacturer;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setManufacturer($manufacturer) {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Return property.
     * 
     * @return array
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * Checks if product has any image.
     * 
     * @return bool
     */
    public function hasImages() {
        return count($this->getImages()) ? true : false;
    }

    /**
     * Adds image.
     * 
     * @param mixed $image
     * $param string $type
     */
    public function addImage($image, $type = Product\IImage::TYPE_GALLERY) {
        if (!($image instanceof Product\IImage)) {
            // object that is not instance of IImage cannot be used
            if (is_object($image)) {
                throw new \Nette\NotSupportedException(sprintf("Expected Product\IImage instance or string, '%s' given", get_class($image)));
            }

            // lets create new image from given string
            $image = new Product\Image($image, $type);
        } else {
            $type = $image->type;
        }


        // for gallery images we create collection
        if (($type === Product\IImage::TYPE_GALLERY)) {
            if (!isset($this->images[$type])) {
                $this->images[$type] = array();
            }

            $this->images[$type][$image->name] = $image;
        } else {
            $this->images[$type] = $image;
        }
    }

    /**
     * Adds description.
     * 
     * @param \Gateway\DataSource\Entity\Product\Description
     */
    public function addDescription(Product\Description $description) {
        $this->descriptions[$description->lang] = $description;
    }

    /**
     * Return property.
     * 
     * @return array
     */
    public function getDescriptions() {
        return $this->hasDescription() ? $this->descriptions : false;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getDescription($lang = Product\Description::NOT_LOCALIZED) {
        if ($this->hasDescription($lang)) {
            return $this->descriptions[$lang];
        }

        return false;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function hasDescription($lang = false) {
        // false = any
        if (!count($this->descriptions)) {
            return false;
        }

        // or desc for current lang
        if ($lang && !isset($this->descriptions[$lang])) {
            return false;
        }

        return true;
    }

    /**
     * Sets descriptions.
     * 
     * @param array
     */
    public function setDescriptions($descriptions) {
        $this->descriptions = $descriptions;
    }

    /**
     * Return property or localized subproperty.
     * 
     * @param string $lang
     * @return array
     */
    public function getCategories($lang = false) {
        if (!$lang && ($lang !== 0)) {
            return $this->categories;
        }

        if (isset($this->categories[$lang])) {
            return $this->categories[$lang];
        }

        // fixing non existing localized categories when categories are eg. brands
        if (isset($this->categories[\Gateway\DataSource\Entity\Localized::NOT_LOCALIZED])) {
            return $this->categories[\Gateway\DataSource\Entity\Localized::NOT_LOCALIZED];
        }

        return array();
    }

    /**
     * Sets categories
     * 
     * @param array
     */
    public function setCategories($categories) {
        $this->categories = $categories;
    }

    /**
     * Adds attribute.
     * 
     * @param \Gateway\DataSource\Entity\Product\Category
     */
    public function addCategory(Product\Category $category) {
        $category->addProduct($this);

        $this->categories[$category->lang][] = $category;
    }

    /**
     * Return property or localized subproperty.
     * 
     * @param string $lang
     * @return array
     */
    public function getAttributes($lang = false) {
        if (!$lang && ($lang !== 0)) {
            return $this->attributes;
        }

        if (isset($this->attributes[$lang])) {
            return $this->attributes[$lang];
        }

        return array();
    }

    /**
     * Adds attribute.
     * 
     * @param \Gateway\DataSource\Entity\Product\Attribute
     */
    public function addAttribute(Product\Attribute $attribute) {
        $this->attributes[$attribute->lang][$attribute->code] = $attribute;
    }

    /**
     * Return property.
     * 
     * @return float
     */
    public function getTax() {
        return $this->tax;
    }

    /**
     * Sets property.
     * 
     * @param float
     */
    public function setTax($tax) {
        $this->tax = $tax;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function getTaxClassId() {
        return $this->taxClassId;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setTaxClassId($taxClassId) {
        if ($taxClassId instanceof \Gateway\DataSource\Entity\Product\TaxClass) {
            $this->taxClassId = $taxClassId;
        } else {
            $this->taxClassId = new \Gateway\DataSource\Entity\Product\TaxClass($taxClassId);
        }
    }	
	
    /**
     * Return property.
     * 
     * @return float
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * Sets property.
     * 
     * @param float
     */
    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    /**
     * Return property.
     * 
     * @return bool
     */
    public function isPriceInclTax() {
        return $this->isPriceInclTax;
    }

    /**
     * Sets property.
     * 
     * @param bool
     */
    public function setIsPriceInclTax($priceInclTax = true) {
        $this->isPriceInclTax = $priceInclTax;
    }

    /**
     * Return property.
     * 
     * @return float
     */
    public function getFinalPrice() {
        return $this->finalPrice;
    }

    public function setFinalPrice($finalPrice) {
        $this->finalPrice = $finalPrice;
    }

    /**
     * Unique key used in DataSource.
     * 
     * @return string
     */
    public function getKey() {
        return $this->getSku();
    }

}
