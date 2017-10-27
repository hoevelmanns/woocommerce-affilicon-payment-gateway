<?php
/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 10.10.17
 * Time: 14:30
 */

namespace Affilicon\ApiClient\Exceptions;


class KeyHasUseException extends ClientExceptions
{
  /**
   * KeyHasUseException constructor.
   * @param $message
   */
  public function __construct($message)
  {
    parent::__construct("Key $message already in use.", 2);
  }
}