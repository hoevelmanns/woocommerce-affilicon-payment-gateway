<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Request.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        25.10.17
 */

namespace Artsolution\AffiliconApiClient\Interfaces;

interface HttpServiceInterface
{

  /**
   * @param string $route
   * @param array $body
   * @return Response
   */
  public function post($route, $body = []);

  /**
   * @param string $route
   * @return object
   */
  public function get($route);

  /**
   * @param string $route
   * @param array $body
   * @return object
   */
  public function put($route, $body = []);

  /**
   * @param string $route
   * @param array $body
   * @return object
   */
  public function delete($route, $body = []);

  /**
   * @param string $route
   * @param array $body
   * @return object
   */
  public function patch($route, $body);

  /**
   * @return array
   */
  public function getData();

  /**
   * @param array $headers
   */
  public function setHeaders($headers);

  /**
   * @return array
   */
  public function getHeaders();

}