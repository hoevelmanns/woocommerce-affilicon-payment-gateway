<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        ClientExceptions.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        25.10.17
 */

namespace AffiliconApiClient\Exceptions;


use AffiliconApiClient\Configurations\Config;
use Throwable;

class ClientExceptions extends \Exception
{
  public function __construct($message = "", $code = 0, Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
    error_log($this->getTraceAsString(), 3, Config::get('error_log.path'));
  }
}