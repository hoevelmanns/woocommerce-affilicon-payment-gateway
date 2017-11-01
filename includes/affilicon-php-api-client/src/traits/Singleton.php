<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Singleton.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        27.10.17
 */

namespace AffiliconApiClient\Traits;


use ReflectionClass;

/**
 * Trait Singleton
 * @package AffiliconApiClient\Traits
 */
trait Singleton {

  protected static $instance;

  final public static function getInstance()
  {
    if (!isset(self::$instance)) {
      $class = new ReflectionClass(__CLASS__);
      self::$instance = $class->newInstanceArgs(func_get_args());
    }

    return self::$instance;
  }

  final private function __clone() { }

  final private function __wakeup() { }

}