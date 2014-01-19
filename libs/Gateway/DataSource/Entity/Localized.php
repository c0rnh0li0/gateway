<?php

namespace Gateway\DataSource\Entity;

/**
 * Localized entity class.
 *
 * @author Lukas Bruha
 */
abstract class Localized extends \Gateway\DataSource\Entity implements \Gateway\Mapping\ILocalizable {

    const NOT_LOCALIZED = 'default';

    /**
     * Language code.
     *
     * @var string
     */
    protected $lang;

    /**
     * Sets language in constructor.
     * 
     * @return string
     */
    public function __construct($lang = self::NOT_LOCALIZED) {
        $this->lang = (($lang !== null) || ($lang !== false)) ? $lang : self::NOT_LOCALIZED;
    }

    /**
     * Get property.
     * 
     * @return string
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setLang($lang) {
        $this->lang = $lang;
    }

}

