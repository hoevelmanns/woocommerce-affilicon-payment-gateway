<?php

namespace AffiliconApiClient\Exceptions;


class KeyInvalidException extends ClientExceptions
{
  /**
   * KeyInvalidException constructor.
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct("Invalid key" . $message, 1);
  }
}