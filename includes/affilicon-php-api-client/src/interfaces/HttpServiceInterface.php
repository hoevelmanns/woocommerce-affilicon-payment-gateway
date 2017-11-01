<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Request.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        25.10.17
 */

namespace AffiliconApiClient\Interfaces;

interface HttpServiceInterface
{

  /**
   * @param $endpoint
   * @return mixed
   */
  public static function init($endpoint);

  /**
   * @param string $route
   * @param array $body
   * @return $this
   */
  public static function post($route, $body = []);

  /**
   * @param string $route
   * @return object
   */
  public static function get($route);

  /**
   * @param string $route
   * @param array $body
   * @return object
   */
  public static function put($route, $body = []);

  /**
   * @param string $route
   * @param array $body
   * @return object
   */
  public static function delete($route, $body = []);

  /**
   * @param string $route
   * @param array $body
   * @return object
   */
  public static function patch($route, $body);

  /**
   * @return array
   */
  public static function getData();

  /**
   * @param array $headers
   */
  public static function setHeaders($headers);

  /**
   * @return array
   */
  public static function getHeaders();

}