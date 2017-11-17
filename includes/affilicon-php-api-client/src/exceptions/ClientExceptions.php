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


use AffiliconApiClient\Services\ConfigService;
use Throwable;

class ClientExceptions extends \Exception
{
  public function __construct($message = "", $code = 0, Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);

    $config = new ConfigService();

    $logPath = $config->get('error_log.path');

    if (empty($logPath) || !is_string($logPath)) {
        $logPath = null;
    }

    error_log($this->getTraceAsString(), 3, $logPath);
  }
}