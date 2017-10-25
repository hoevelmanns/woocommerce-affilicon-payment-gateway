<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Api.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace Affilicon;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class ApiClient
 * @package Affilicon
 */

class Client
{
  protected $token;
  protected $username;
  protected $password;
  public $clientId;
  public $countryId;
  public $userLanguage;
  public static $instance;
  /** @var  Cart */
  public static $cart;

  /** @var \GuzzleHttp\Client $httpClient */
  protected $httpClient;

  public function __construct()
  {
    self::$instance = $this;
  }

  public static function getInstance()
  {
    {
      if (self::$instance === null) {
        self::$instance = new self();
      }
      return self::$instance;
    }
  }

  public function init()
  {
    $this->authenticate();
    return $this;
  }

  /**
   * Add the request headers
   * @return array
   */
  public function headers()
  {
    return [
      'Authorization' => 'Bearer ' . $this->token ?? '',
      'username' => $this->username,
      'password' => $this->password
    ];
  }

  /**
   * @return bool
   */
  public function isAuthenticated()
  {
    return !is_null($this->token);
  }

  /**
   * authenticate to api
   * @return mixed
   * @throws \ErrorException
   */
  public function authenticate()
  {
    if ($this->isAuthenticated()) {
      return $this->getToken();
    }

    $member = isset($this->username) && isset($this->password);

    $request = Request::getInstance();

    try {
      $response =  $request->post(AFFILICON_API['routes']['auth'][$member ? 'member' : 'anonymous'], [], $this->headers());
    } catch (\Exception $e) {
      return new \ErrorException('affilicon_payment_error_authentication_failed: ' . $e->getMessage(), $e->getCode());
    }

    if (!$response || !$response->token) {
      throw new \ErrorException('affilicon_payment_error_authentication_failed: token invalid', 403);
    }

    return $this->token = $response->token;
  }

  /**
   * @param $username
   */
  public function setUserName($username)
  {
    $this->userName = $username;
  }

  /**
   * @return mixed
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @param $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }


  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * set the Client ID, previously called Vendor ID
   * @param $id
   * @return $this
   */
  public function setClientId($id)
  {
    $this->clientId = $id;
    return $this;
  }

  /**
   * get the Client ID, previously called Vendor ID
   * @return mixed
   */
  public function getClientId()
  {
    return $this->clientId;
  }

  /**
   * @return mixed
   */
  public function getCountryId()
  {
    return $this->countryId;
  }

  /**
   * @param $countryId
   * @return $this
   */
  public function setCountryId($countryId)
  {
    $this->countryId = $countryId;
    return $this;
  }

  /**
   * @return string
   */
  public function getUserLanguage()
  {
    return $this->userLanguage;
  }

  /**
   * @param $userLanguage
   * @return $this
   */
  public function setUserLanguage($userLanguage)
  {
    $this->userLanguage = $userLanguage;
    return $this;
  }

}