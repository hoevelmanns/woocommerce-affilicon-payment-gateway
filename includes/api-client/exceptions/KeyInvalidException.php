<?php

namespace Affilicon;


class KeyInvalidException extends \Exception
{
  /**
   * KeyInvalidException constructor.
   * @param string $string
   */
  public function __construct($string)
  {
    parent::__construct("Invalid key" . $string, 1);
    error_log($this->getTraceAsString(), 3, '/tmp/my_exception.log');
  }
}