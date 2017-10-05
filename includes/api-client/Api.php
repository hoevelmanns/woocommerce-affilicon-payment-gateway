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
   * @return mixed|WP_Error
   */
  public function authenticate()
  {

    if ($this->isAuthenticated()) {
      return $this->getToken();
    }

    try {
      $response = $this->post(AFFILICON_ROUTES['auth']);
    } catch (Exception $e) {
      return new WP_Error('affilicon_payment_error_authentication_failed', $e->getMessage(), array('status' => $e->getCode()));
    }

    if (!$response || !$response['token']) {
      return new WP_Error('affilicon_payment_error_authentication_failed', 'token invalid');
    }

    return $this->token = $response['token'];

  }

  public function getToken()
  {
    return $this->token;
  }

}