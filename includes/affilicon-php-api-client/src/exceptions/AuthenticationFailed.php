<?php

namespace Affilicon\ApiClient\Exceptions;


class AuthenticationFailed extends ClientExceptions
{
  /**
   * AuthenticationFailed constructor.
   * @param string $message
   * @param integer $code
   */
  public function __construct($message, $code)
  {
    parent::__construct("authentication failed: " . $message, $code);
  }
}