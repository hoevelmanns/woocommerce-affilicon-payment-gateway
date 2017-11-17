<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        HasEncryption.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        17.11.17
 */

namespace AffiliconApiClient\Traits;


use AffiliconApiClient\Client;
use AffiliconApiClient\Exceptions\ConfigurationInvalid;

trait HasEncryption
{
    /** @var  string */
    protected $cryptMethod;
    /** @var  string */
    protected $cryptKey;

    /** @var  Client */
    protected $client;

    protected function init()
    {
        $this->cryptKey = $this->client->getSecretKey();

        $cryptMethod = $this->client->config()->get('security.crypt_method');

        if (!is_string($cryptMethod)) {
            throw new ConfigurationInvalid('Crypt method must be a string.');
        }

        $this->cryptMethod = $this->client->config()->get('security.crypt_method');
    }
    /**
     * Returns an encrypted string
     * @param string $data json encoded prefill data
     * @return string
     */
    protected function encrypt($data)
    {
        $this->init();
        return urlencode(openssl_encrypt($data, $this->cryptMethod, $this->cryptKey));
    }

    /**
     * Decrypt a given string
     * @param $data
     * @return string
     */
    protected function decrypt($data)
    {
        $this->init();
        return urlencode(openssl_decrypt($data, $this->cryptMethod, $this->cryptKey));
    }
}