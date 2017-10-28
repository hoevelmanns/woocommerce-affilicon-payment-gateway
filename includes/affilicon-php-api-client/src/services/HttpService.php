<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        HttpService.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        25.10.17
 */

namespace Artsolution\AffiliconApiClient\Services;


use Artsolution\AffiliconApiClient\Abstracts\AbstractHttpService;
use Artsolution\AffiliconApiClient\Interfaces\HttpServiceInterface;
use Artsolution\AffiliconApiClient\Traits\Singleton;

class HttpService extends AbstractHttpService implements HttpServiceInterface
{
  use Singleton;
}