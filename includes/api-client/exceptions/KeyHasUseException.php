<?php
/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 10.10.17
 * Time: 14:30
 */

namespace Affilicon;


class KeyHasUseException extends \Exception
{
  /**
   * KeyHasUseException constructor.
   * @param $key
   */
  public function __construct($key)
  {
    parent::__construct("Key $key already in use.", 2);
    error_log($this->getTraceAsString(), 3, '/tmp/my_exception.log');
  }
}