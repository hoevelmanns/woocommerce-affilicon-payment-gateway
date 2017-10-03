<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Api.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */


if (!defined('ABSPATH')) {
  exit;
}

class AffiliconApi
{

  public $token;
  public $gateway;

  const ENDPOINT = "https://service.affilicon.net/api"; // todo in options?

  public function __construct(WC_Affilicon_Payment_Gateway $gateway)
  {

    define('ROUTES', [
      'auth' => '/auth/anonymous/token',
      'refresh' => '/auth/refresh'
    ]);

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
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token
        ],
        'body' => $args
    ]);

    return json_decode(wp_remote_retrieve_body($response), true);

  }

  /**
   * authenticate to api
   * @return mixed|WP_Error
   */
  public function authenticate()
  {

    try{
      $response = $this->post(ROUTES['auth']);
    }catch (Exception $e) {
      return new WP_Error('affilicon_payment_error_authentication_failed', $e->getMessage(), array( 'status' => $e->getCode() ));
    }

    if (!$response || !$response['token']) {
      return new WP_Error('affilicon_payment_error_authentication_failed', 'token invalid');
    }

    return $this->token = $response['token'];
  }

}