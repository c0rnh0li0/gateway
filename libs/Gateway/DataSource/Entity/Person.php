<?php

namespace Gateway\DataSource\Entity;

/**
 * General person entity
 *
 * @author Lukas Bruha
 */
class Person extends \Gateway\DataSource\Entity {

    const TYPE_GENDER_MALE = 'male';
    const TYPE_GENDER_FEMALE = 'female';
    const TYPE_GENDER_UNDEFINED = 'undefined';

    /**
     * ID.
     *
     * @var int
     */
    protected $id;

    /**
     * Firstname.
     *
     * @var string
     */
    protected $firstname;

    /**
     * Surname.
     *
     * @var string
     */
    protected $surname;

    /**
     * Gender.
     *
     * @var string
     */
    protected $gender;

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
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getSurname() {
        return $this->surname;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setSurname($surname) {
        $this->surname = $surname;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * Sets property.
     * 
     * @param string
     */
    public function setGender($gender) {
        $this->gender = $gender;
    }

    /**
     * Return property.
     * 
     * @return string
     */
    public function getFullName() {
        return $this->firstname . ' ' . $this->surname;
    }

}
