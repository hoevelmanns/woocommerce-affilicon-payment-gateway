<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        AddressInterfacee.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        23.10.17
 */

namespace Artsolution\AffiliconApiClient\Interfaces;


interface AddressInterface
{
  /**
   * @return string
   */
  public function getAddressType();

  /**
   * @param string $addressType
   */
  public function setAddressType($addressType);

  /**
   * @return string
   */
  public function getCompany();

  /**
   * @param string $company
   */
  public function setCompany($company);

  /**
   * @return string
   */
  public function getFirstname();

  /**
   * @param string $firstname
   */
  public function setFirstname($firstname);

  /**
   * @return string
   */
  public function getLastname();

  /**
   * @param string $lastname
   */
  public function setLastname($lastname);

  /**
   * @return string
   */
  public function getAddress1();

  /**
   * @param string $address_1
   */
  public function setAddress1($address_1);

  /**
   * @return string
   */
  public function getAddress2();

  /**
   * @param string $address_2
   */
  public function setAddress2($address_2);

  /**
   * @return string
   */
  public function getCity();

  /**
   * @param string $city
   */
  public function setCity($city);

  /**
   * @return string
   */
  public function getPostcode();

  /**
   * @param string $postcode
   */
  public function setPostcode($postcode);

  /**
   * @return string
   */
  public function getCountry();

  /**
   * @param string $country
   */
  public function setCountry($country);

  /**
   * @return string
   */
  public function getPhone();

  /**
   * @param string $phone
   */
  public function setPhone($phone);

  /**
   * @return string
   */
  public function getFax();

  /**
   * @param string $fax
   */
  public function setFax($fax);

  /**
   * @return string
   */
  public function getMobile();

  /**
   * @param string $mobile
   */
  public function setMobile($mobile);

  /**
   * @return string
   */
  public function getEmail();

  /**
   * @param string $email
   */
  public function setEmail($email);
}