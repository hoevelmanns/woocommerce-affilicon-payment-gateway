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
  private $clientId;
  private $countryId;
  private $userLanguage;

  public function __construct()
  {
    $this->authenticate();
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
   * post request
   * @param $route
   * @param array $args
   * @return array|mixed|object
   */
  public function post($route, array $args = [])
  {
    $url = AFFILICON_SERVICE_URL . $route;

    // todo replace wp_remote_post with native post method or Guzzle
    $response = wp_remote_post($url, [
      'method' => 'POST',
      'headers' => $this->headers(),
      'body' => $args
    ]);

    return $this->responseBody($response);
  }

  /**
   * @param $route
   * @param array $args
   */
  public function put($route, array $args = [])
  {
    // todo put request method
  }

  /**
   * get request
   * @param $route
   * @return object
   */
  public function get($route)
  {
    $url = AFFILICON_SERVICE_URL . $route;

    // todo replace wp_remote_get with native get method or Guzzle
    $response = wp_remote_get($url, [
      'method' => 'GET',
      'headers' => $this->headers()
    ]);

    return $this->responseBody($response);
  }

  /**
   * Return the request body
   * @param $response
   * @return object
   */
  private function responseBody($response)
  {
    $responseBody = json_decode(wp_remote_retrieve_body($response), true);
    $responseBody['data'] = (object) $responseBody['data'];
    return (object) $responseBody;
  }

  /**
   * Add the request headers
   * @return array
   */
  private function headers()
  {
    return [
      'Authorization' => 'Bearer ' . $this->token,
      'username' => $this->username,
      'password' => $this->password
    ];
  }

  /**
   * @return bool
   */
  private function isAuthenticated()
  {
    return !is_null($this->token);
  }

  /**
   * authenticate to api
   * @return \ErrorException
   */
  public function authenticate()
  {
    if ($this->isAuthenticated()) {
      return $this->getToken();
    }

    $member = isset($this->username) && isset($this->password);

    try {
      $response = $this->post(AFFILICON_API['routes']['auth'][$member ? 'member' : 'anonymous']);
    } catch (\Exception $e) {
      return new \ErrorException('affilicon_payment_error_authentication_failed', $e->getMessage(), array('status' => $e->getCode()));
    }

    if (!$response || !$response->token) {
      return new \ErrorException('affilicon_payment_error_authentication_failed', 'token invalid');
    }

    return $this->token = $response->token;
  }

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
   * @return mixed
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