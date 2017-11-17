<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Api.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        10.10.17
 */

namespace AffiliconApiClient\Abstracts;

use AffiliconApiClient\Interfaces\AddressInterface;

/**
 * Class Address
 * @package Affilicon
 *
 * @property string $company
 * @property string $firstname
 * @property string $lastname
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $postcode
 * @property string $country
 * @property string $phone
 * @property string $fax
 * @property string $mobile
 * @property string $email
 */
abstract class AbstractAddress extends AbstractModel implements AddressInterface
{
    /** @var array */
    protected $data;

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->data['company'];
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->data['company'] = $company;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->data['firstname'];
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->data['firstname'] = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->data['lastname'];
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->data['lastname'] = $lastname;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->data['address_2'];
    }

    /**
     * @param string $address_1
     */
    public function setAddress1($address_1)
    {
        $this->data['address_1'] = $address_1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->data['address_2'];
    }

    /**
     * @param string $address_2
     */
    public function setAddress2($address_2)
    {
        $this->data['address_2'] = $address_2;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->data['city'];
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->data['city'] = $city;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->data['postcode'];
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->data['postcode'] = $postcode;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->data['country'];
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->data['country'] = $country;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->data['phone'];
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->data['phone'] = $phone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->data['fax'];
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->data['fax'] = $fax;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->data['mobile'];
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->data['mobile'] = $mobile;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->data['email'];
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->data['email'] = $email;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function transform()
    {
        $data = [];

        $address = $this->getData();

        // split camel case
        $type = strtolower(preg_split('/(?=[A-Z])/', get_class_name($this))[1]);

        $mapper = $this->client->config()->get("address.$type");

        foreach($address as $key => $item) {

            if (!empty($item)) {

                $data[$mapper[$key]] = $item;

            }

        }

        return $data;

    }
}
