<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        ClientInterface.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        24.10.17
 */

namespace Artsolution\AffiliconApiClient\Interfaces;

use Artsolution\AffiliconApiClient\Exceptions\AuthenticationFailed;

/**
 * Interface ClientInterface
 * @package Affilicon
 */

interface ClientInterface
{

  /**
   * Initializes the Client
   * @return $this
   */
  public function init();

  /**
   * Sets the Client ID, previously called Vendor ID
   * @param string $id
   * @return $this
   */
  public function setClientId($id);

  /**
   * Gets the Client ID, previously called Vendor ID
   * @return string
   */
  public function getClientId();

  /**
   * Gets the specified country code
   * @return string
   */
  public function getCountryId();

  /**
   * Sets the country code, eg. "en-US"
   * @param $countryId
   * @return $this
   */
  public function setCountryId($countryId);

  /**
   * Gets the specified user language
   * @return $this
   */
  public function getUserLanguage();

  /**
   * Sets the user language, eg. "en"
   * @param string $userLanguage
   * @return $this
   */
  public function setUserLanguage($userLanguage);

}