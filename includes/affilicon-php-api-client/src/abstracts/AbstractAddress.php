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
  protected $addressType;

  const ADDRESS_TYPE_SHIPPING = 'shipping';
  const ADDRESS_TYPE_BILLING = 'billing';
  const ADDRESS_TYPE_BASIC = 'basic';

  /**
   * @return string
   */
  public function getAddressType()
  {
    return $this->addressType;
  }

  /**
   * @param string $addressType
   */
  public function setAddressType($addressType)
  {
    $this->addressType = $addressType;
  }

  /**
   * @return string
   */
  public function getCompany()
  {
    return $this->company;
  }

  /**
   * @param string $company
   */
  public function setCompany($company)
  {
    $this->company = $company;
  }

  /**
   * @return string
   */
  public function getFirstname()
  {
    return $this->firstname;
  }

  /**
   * @param string $firstname
   */
  public function setFirstname($firstname)
  {
    $this->firstname = $firstname;
  }

  /**
   * @return string
   */
  public function getLastname()
  {
    return $this->lastname;
  }

  /**
   * @param string $lastname
   */
  public function setLastname($lastname)
  {
    $this->lastname = $lastname;
  }

  /**
   * @return string
   */
  public function getAddress1()
  {
    return $this->address_1;
  }

  /**
   * @param string $address_1
   */
  public function setAddress1($address_1)
  {
    $this->address_1 = $address_1;
  }

  /**
   * @return string
   */
  public function getAddress2()
  {
    return $this->address_2;
  }

  /**
   * @param string $address_2
   */
  public function setAddress2($address_2)
  {
    $this->address_2 = $address_2;
  }

  /**
   * @return string
   */
  public function getCity()
  {
    return $this->city;
  }

  /**
   * @param string $city
   */
  public function setCity($city)
  {
    $this->city = $city;
  }

  /**
   * @return string
   */
  public function getPostcode()
  {
    return $this->postcode;
  }

  /**
   * @param string $postcode
   */
  public function setPostcode($postcode)
  {
    $this->postcode = $postcode;
  }

  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }

  /**
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }

  /**
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }

  /**
   * @param string $phone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
  }

  /**
   * @return string
   */
  public function getFax()
  {
    return $this->fax;
  }

  /**
   * @param string $fax
   */
  public function setFax($fax)
  {
    $this->fax = $fax;
  }

  /**
   * @return string
   */
  public function getMobile()
  {
    return $this->mobile;
  }

  /**
   * @param string $mobile
   */
  public function setMobile($mobile)
  {
    $this->mobile = $mobile;
  }

  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

}
