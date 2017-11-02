<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        HttpService.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        25.10.17
 */

namespace AffiliconApiClient\Services;


use AffiliconApiClient\Interfaces\HttpServiceInterface;
use AffiliconApiClient\Traits\Singleton;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class HttpService implements HttpServiceInterface
{
  /** @var Client */
  protected static $HttpClient;
  protected static $endpoint;
  /** @var  Response $response */
  protected $response;
  protected $headers;

  use Singleton;

  /**
   * @param $endpoint
   * @return mixed
   */
  public static function init($endpoint)
  {
    self::getInstance();

    static::$endpoint = $endpoint;
    static::$HttpClient = new Client();

    return self::$instance;
  }

  /**
   * @param array $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }

  /**
   * @return mixed
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * @return object
   */
  public function getData()
  {
    $responseBody = json_decode($this->response->getBody(), true);

    if (array_exists('data', $responseBody)) {
      $responseBody['data'] = (object) $responseBody['data'];
    }

    return (object) $responseBody;
  }

  /**
   * @param string $route
   * @param array $body
   * @return $this
   */
  public function post($route, $body = [])
  {
    $url = static::$endpoint . $route;

    $this->response = static::$HttpClient->request('POST', $url, [
      'headers' => $this->getHeaders(),
      'json' => $body
    ]);

    return self::$instance;
  }

  /**
   * @param string $route
   * @return $this
   */
  public function get($route)
  {
    $url = static::$endpoint . $route;

    $this->response = static::$HttpClient->request('GET', $url, [
      'headers' => $this->getHeaders()
    ]);

    return self::$instance;
  }

  public function put($route, $body = []){
    // todo Implement put method;
  }

  public function patch($route, $body)
  {
    //todo Implement patch method
  }

  public function delete($route, $body = [])
  {
    // todo Implement delete() method.
  }

}