<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Client.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace Artsolution\AffiliconApiClient;

use Artsolution\AffiliconApiClient\Exceptions\AuthenticationFailed;
use Artsolution\AffiliconApiClient\Interfaces\ClientInterface;
use Artsolution\AffiliconApiClient\Services\HttpService;
use Artsolution\AffiliconApiClient\Traits\Environment;
use Artsolution\AffiliconApiClient\Traits\Singleton;

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
  public $clientId;
  public $countryId;
  public $userLanguage;

  /** @var  HttpService */
  protected $HttpService;

  use Singleton;
  use Environment;


  public function init()
  {
    $this->setEnvironment();
    $this->HttpService = HttpService::getInstance();
    $this->HttpService->init($this->getEnvironmentConfigByKey('service_url'));
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