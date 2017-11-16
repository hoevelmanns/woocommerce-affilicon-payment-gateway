<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Client.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace AffiliconApiClient;

use AffiliconApiClient\Interfaces\ClientInterface;
use AffiliconApiClient\Services\HttpService;
use AffiliconApiClient\Services\AuthService;
use AffiliconApiClient\Traits\Environment;
use AffiliconApiClient\Traits\Singleton;

/**
 * Class Client
 * @package AffiliconApiClient
 */
class Client implements ClientInterface
{
    /** @var string */
    public $clientId;
    /** @var string */
    public $countryId;
    /** @var string */
    public $userLanguage;
    /** @var string */
    private $secretKey;

    /** @var  AuthService */
    protected $auth;

    /** @var  HttpService */
    protected $http;

    use Singleton;
    use Environment;

    /**
     * Initializes the Client
     * @return $this
     */
    public function init()
    {
        $this->setEnvironment();

        $this->http = new HttpService($this->environment->service_url);

        $this->auth = (new AuthService($this))
            ->anonymous()
            ->authenticate();

        return $this;
    }

    public function http()
    {
        return $this->http;
    }

    public function auth()
    {
        return $this->auth;
    }

    /**
     * Sets the Client ID, previously called Vendor ID
     * @param string $id
     * @return $this
     */
    public function setClientId($id)
    {
        $this->clientId = $id;
        return $this;
    }

    /**
     * Gets the Client ID, previously called Vendor ID
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Gets the specified country code
     * @return string
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Sets the country code, eg. "en-US"
     * @param $countryId
     * @return $this
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * Gets the specified user language
     * @return string
     */
    public function getUserLanguage()
    {
        return $this->userLanguage;
    }

    /**
     * Sets the user language, eg. "en"
     * @param string $userLanguage
     * @return $this
     */
    public function setUserLanguage($userLanguage)
    {
        $this->userLanguage = $userLanguage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->auth()->getToken();
    }

    /**
     * Sets the api secret key
     * @param $secretKey
     * @return $this
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * Gets the api secret key
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

}