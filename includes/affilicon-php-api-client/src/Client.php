<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Client.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace Affilicon\ApiClient;

use Affilicon\ApiClient\Exceptions\AuthenticationFailed;
use Affilicon\ApiClient\Interfaces\ClientInterface;
use Affilicon\ApiClient\Services\HttpService;
use Affilicon\ApiClient\Traits\Singleton;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class ApiClient
 * @package Affilicon
 */

class Client implements ClientInterface
{
  protected $token;
  protected $username;
  protected $password;
  protected $environment;
  public $clientId;
  public $countryId;
  public $userLanguage;

  /** @var  HttpService */
  protected $HttpService;

  use Singleton;

  /**
   * Sets the environment, default 'production'
   * @param string $environment
   * @return $this
   *
   */
  public function setEnv($environment = null)
  {
    $this->environment = $environment ? $environment: 'production';
    return $this;
  }

  /**
   * Gets the environment
   * @return object
   */
  public function getEnvName()
  {
    return $this->environment;
  }

  /**
   * @param $key
   * @return object
   */
  public function getEnvConfigByKey($key)
  {
    return CONFIG['environment'][$this->environment][$key];
  }

  public function init()
  {
    $this->setEnv();
    $this->HttpService = HttpService::getInstance();
    $this->HttpService->init($this->getEnvConfigByKey('service_url'));
    $this->authenticate();
    return $this;
  }

  public function isAuthenticated()
  {
    return !is_null($this->token);
  }
  
  public function authenticate()
  {
    if ($this->isAuthenticated()) {
      return $this->getToken();
    }

    $member = isset($this->username) && isset($this->password);

    try {
      $authType = $member ? 'member' : 'anonymous';

      $data = $this->HttpService
        ->post(API['routes']['auth'][$authType])
        ->getData();

    } catch (\Exception $e) {
      throw new AuthenticationFailed($e->getMessage(), $e->getCode());
    }

    if (!$data || !$data->token) {
      throw new AuthenticationFailed('token invalid', 403);
    }

    $this->HttpService
      ->setHeaders([
        'Authorization' => 'Bearer ' . $data->token,
        'username' => $this->username,
        'password' => $this->password
      ]);

    return $this->token = $data->token;
  }

  public function setUserName($username)
  {
    $this->username = $username;
    return $this;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function setPassword($password)
  {
    $this->password = $password;
    return $this;
  }

  public function getToken()
  {
    return $this->token;
  }

  public function setClientId($id)
  {
    $this->clientId = $id;
    return $this;
  }

  public function getClientId()
  {
    return $this->clientId;
  }

  public function getCountryId()
  {
    return $this->countryId;
  }

  public function setCountryId($countryId)
  {
    $this->countryId = $countryId;
    return $this;
  }

  public function getUserLanguage()
  {
    return $this->userLanguage;
  }

  public function setUserLanguage($userLanguage)
  {
    $this->userLanguage = $userLanguage;
    return $this;
  }


}