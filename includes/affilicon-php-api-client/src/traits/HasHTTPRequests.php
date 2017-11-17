<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        HasHTTPRequests.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        04.11.17
 */

namespace AffiliconApiClient\Traits;


use AffiliconApiClient\Exceptions\ConfigurationInvalid;
use AffiliconApiClient\Services\HttpService;

/**
 * Trait HasRequests
 * @package AffiliconApiClient\Traits
 */
trait HasHTTPRequests
{
    /** @var  string */
    protected $route;


}