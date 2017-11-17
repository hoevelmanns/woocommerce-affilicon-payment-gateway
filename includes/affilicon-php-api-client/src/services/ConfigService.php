<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file   Config.php
 * @author Marcelle Hövelmanns
 * @site   http://www.artsolution.de
 * @date   29.10.17
 */

namespace AffiliconApiClient\Services;


use AffiliconApiClient\Exceptions\ConfigurationInvalid;

/**
 * Class ConfigService
 * @package AffiliconApiClient\Services
 */
class ConfigService
{
    protected $data;

    public function __construct()
    {
        try {

            $global = include __DIR__ . "/../config/config.php";
            $routes = include __DIR__ . "/../config/routes.php";

        } catch (\Exception $e) {

            throw new ConfigurationInvalid('configuration is missing or invalid');

        }

        $this->data = array_merge($global, $routes);
    }

    /**
     * @param $key
     * @return array|string
     */
    public function get($key)
    {
        $config = array_get($this->data, $key);

        return $config;
    }

}