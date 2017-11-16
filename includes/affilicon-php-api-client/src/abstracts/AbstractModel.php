<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Model.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        22.10.17
 */

namespace AffiliconApiClient\Abstracts;


use AffiliconApiClient\Client;
use AffiliconApiClient\Exceptions\ConfigurationInvalid;
use AffiliconApiClient\Interfaces\ModelInterface;
use AffiliconApiClient\Models\Collection;
use AffiliconApiClient\Services\HttpService;

abstract class AbstractModel implements ModelInterface
{
    /** @var string */
    protected $route;

    /** @var Collection */
    protected $rows;

    /** @var Client */
    protected $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
        $this->setRoute();
    }

    /**
     * @param array $body
     * @return HttpService
     */
    protected function post($body)
    {
        return $this->client
            ->http()->post($this->route, $body);
    }

    protected function get()
    {
        return $this->client
            ->http()->get($this->route);
    }

    /**
     * Sets the resource for the model
     * @return string
     * @throws ConfigurationInvalid
     */
    protected function setRoute()
    {
        $class = explode("\\", get_class($this));

        $route = $this->client
            ->config()
            ->get('routes.' . $class[count($class) - 1]);

        if (!is_string($route)) {
            throw new ConfigurationInvalid('Route must be a string');
        }

        $this->route = $route;
    }

    public function findById($id)
    {
        // TODO: Implement findById() method.
    }

    public function find($params, $with)
    {
        // TODO: Implement find() method.
    }

    public function fetch()
    {
        // TODO: Implement all() method.
    }
}