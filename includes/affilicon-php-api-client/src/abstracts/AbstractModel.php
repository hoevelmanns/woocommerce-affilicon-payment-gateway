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
use AffiliconApiClient\Interfaces\ModelInterface;
use AffiliconApiClient\Models\Collection;
use AffiliconApiClient\Traits\HasHTTPRequests;

abstract class AbstractModel implements ModelInterface
{
    /** @var string */
    protected $route;

    /** @var Client */
    protected $client;

    /** @var Collection */
    protected $rows;

    use HasHTTPRequests;

    public function __construct()
    {
        $this->client = Client::getInstance();
        $this->setRoute();
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