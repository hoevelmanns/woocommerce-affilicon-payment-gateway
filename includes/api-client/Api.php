<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Api.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace AffiliconApi;

class AffiliconApi
{

  private $token;
  private $clientId;

  const ENDPOINT = "https://service.affilicon.net/api"; // todo in options?

  public function __construct()
  {

    if (!defined('AFFILICON_ROUTES')) {
      define('AFFILICON_ROUTES', [
        'auth' => '/auth/anonymous/token',
        'refreshToken' => '/auth/refresh',
        'carts' => '/carts',
        'cartItemsProducts' => '/cart-items/products'
      ]);
    }

  }

  /**
   * @param $route
   * @param array $args
   * @return array|mixed|object
   */
  public function post($route, array $args = [])
  {

    $url = self::ENDPOINT . $route;

    // todo replace wp_remote_post with native post method or Guzzle
    $response = wp_remote_post($url, [
      'method' => 'POST',
      'headers' => $this->headers(),
      'body' => $args
    ]);

    return json_decode(wp_remote_retrieve_body($response), true);

  }

  private function headers()
  {
    return [
      'Authorization' => 'Bearer ' . $this->token
    ];
  }

  private function isAuthenticated()
  {
    return !is_null($this->token);
  }

  /**
   * authenticate to api
   * @return mixed|\ErrorException
   */
  public function authenticate()
  {

    if ($this->isAuthenticated()) {
      return $this->getToken();
    }

    try {
      $response = $this->post(AFFILICON_ROUTES['auth']);
    } catch (\Exception $e) {
      return new \ErrorException('affilicon_payment_error_authentication_failed', $e->getMessage(), array('status' => $e->getCode()));
    }

    if (!$response || !$response['token']) {
      return new \ErrorException('affilicon_payment_error_authentication_failed', 'token invalid');
    }

    return $this->token = $response['token'];

  }

  public function getToken()
  {
    return $this->token;
  }

  /**
   * set the Client ID, previously called Vendor ID
   * @param $id
   * @return $this
   */
  public function setClientId($id)
  {
    $this->clientId = $id;
    return $this;
  }

  /**
   * get the Client ID, previously called Vendor ID
   * @return mixed
   */
  public function getClientId()
  {
    return $this->clientId;
  }

}