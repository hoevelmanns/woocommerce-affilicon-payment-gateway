<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file   AuthService.php
 * @author Marcelle Hövelmanns
 * @site   http://www.artsolution.de
 * @date   28.10.17
 */

namespace AffiliconApiClient\Services;


use AffiliconApiClient\Client;
use AffiliconApiClient\Exceptions\AuthenticationFailed;

/**
 * Class Authentication
 *
 * @package AffiliconApiClient\Traits
 */
class AuthService
{
    protected $token;
    protected $username;
    protected $password;
    protected $route;

    /** @var  Client */
    protected $client;

    /**
     * AuthService constructor.
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    public function isAuthenticated()
    {
        return !is_null($this->token);
    }

    public function member()
    {
        $this->route = $this->client
            ->config()
            ->get('routes.auth.member');

        return $this;
    }

    public function employee()
    {
        $this->route = $this->client
            ->config()
            ->get('routes.auth.employee');

        return $this;
    }

    public function anonymous()
    {
        $this->route = $this->client
            ->config()
            ->get('routes.auth.anonymous');

        return $this;
    }

    public function authenticate()
    {
        if ($this->isAuthenticated()) {
            return $this->getToken();
        }

        try {

            $meta = $this->client
                ->http()
                ->post($this->route)
                ->body();

        } catch (\Exception $e) {
            // todo refreshToken if message says token expired
            throw new AuthenticationFailed($e->getMessage(), $e->getCode());
        }

        if (!$meta || !$meta->token) {

            throw new AuthenticationFailed('token invalid', 403);

        }

        $this->client->http()->setHeaders([
            'Authorization' => "Bearer $meta->token"
        ]);

        $this->token = $meta->token;

        return $this;
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
}