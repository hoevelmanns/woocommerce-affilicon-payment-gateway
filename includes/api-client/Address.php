<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Api.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        10.10.17
 */

namespace Affilicon;

class Address extends ApiClient
{
  const ADDRESS_TYPE_SHIPPING = 'shipping';
  const ADDRESS_TYPE_BILLING = 'billing';
  const ADDRESS_TYPE_BASIC = 'basic';

  private $addressType;
  private $company;
  private $firstname;
  private $lastname;
  private $address_1;
  private $address_2;
  private $city;
  private $postcode;
  private $country;
  private $phone;
  private $fax;
  private $mobile;
  private $email;

  /**
   * @return mixed
   */
  public function getAddressType()
  {
    return $this->addressType;
  }

  /**
   * @param mixed $addressType
   */
  public function setAddressType($addressType)
  {
    $this->addressType = $addressType;
  }

  /**
   * @return mixed
   */
  public function getCompany()
  {
    return $this->company;
  }

  /**
   * @param mixed $company
   */
  public function setCompany($company)
  {
    $this->company = $company;
  }

  /**
   * @return mixed
   */
  public function getFirstname()
  {
    return $this->firstname;
  }

  /**
   * @param mixed $firstname
   */
  public function setFirstname($firstname)
  {
    $this->firstname = $firstname;
  }

  /**
   * @return mixed
   */
  public function getLastname()
  {
    return $this->lastname;
  }

  /**
   * @param mixed $lastname
   */
  public function setLastname($lastname)
  {
    $this->lastname = $lastname;
  }

  /**
   * @return mixed
   */
  public function getAddress1()
  {
    return $this->address_1;
  }

  /**
   * @param mixed $address_1
   */
  public function setAddress1($address_1)
  {
    $this->address_1 = $address_1;
  }

  /**
   * @return mixed
   */
  public function getAddress2()
  {
    return $this->address_2;
  }

  /**
   * @param mixed $address_2
   */
  public function setAddress2($address_2)
  {
    $this->address_2 = $address_2;
  }

  /**
   * @return mixed
   */
  public function getCity()
  {
    return $this->city;
  }

  /**
   * @param mixed $city
   */
  public function setCity($city)
  {
    $this->city = $city;
  }

  /**
   * @return mixed
   */
  public function getPostcode()
  {
    return $this->postcode;
  }

  /**
   * @param mixed $postcode
   */
  public function setPostcode($postcode)
  {
    $this->postcode = $postcode;
  }

  /**
   * @return mixed
   */
  public function getCountry()
  {
    return $this->country;
  }

  /**
   * @param mixed $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }

  /**
   * @return mixed
   */
  public function getPhone()
  {
    return $this->phone;
  }

  /**
   * @param mixed $phone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
  }

  /**
   * @return mixed
   */
  public function getFax()
  {
    return $this->fax;
  }

  /**
   * @param mixed $fax
   */
  public function setFax($fax)
  {
    $this->fax = $fax;
  }

  /**
   * @return mixed
   */
  public function getMobile()
  {
    return $this->mobile;
  }

  /**
   * @param mixed $mobile
   */
  public function setMobile($mobile)
  {
    $this->mobile = $mobile;
  }

  /**
   * @return mixed
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param mixed $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

}
