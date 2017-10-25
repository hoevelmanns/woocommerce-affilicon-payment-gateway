<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        WC_Affilicon_Payment_Gateway_API_Client.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        24.10.17
 */

include_once 'api-client/src/config/routes.php';
include_once 'api-client/src/interfaces/ClientInterface.php';
include_once 'api-client/src/interfaces/RequestInterface.php';
include_once 'api-client/src/AbstractRequest.php';
include_once 'api-client/src/interfaces/ResponseInterface.php';
include_once 'api-client/src/interfaces/ProductInterface.php';
include_once 'api-client/src/Client.php';

// todo write wrapper
include_once 'api-client/src/Request.php';
include_once 'api-client/src/Response.php';

include_once 'api-client/src/Collection.php';
include_once 'api-client/src/exceptions/KeyHasUseException.php';
include_once 'api-client/src/exceptions/KeyInvalidException.php';
include_once 'api-client/src/models/AbstractModel.php';
include_once 'api-client/src/models/Cart.php';
include_once 'api-client/src/models/Product.php';
include_once 'api-client/src/models/LineItem.php';

class WC_Affilicon_Payment_Gateway_API_Client_Wrapper extends \Affilicon\Client
{

  /** @var \Affilicon\Cart $cart */
  public static $cart;
  public static $instance;

  public function init()
  {
    parent::init();
  }

  public function post($route, array $args = [])
  {
    $url = AFFILICON_SERVICE_URL . $route;

    $response = wp_remote_post($url, [
      'method' => 'POST',
      'headers' => parent::headers(),
      'body' => $args
    ]);

    return $this->responseBody($response);
  }

  /**
   * Return the request body
   * @param array $response
   * @return object
   */
  public function responseBody($response)
  {
    $responseBody = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($responseBody['data'])) {
      $responseBody['data'] = (object) $responseBody['data'];
    }

    return (object) $responseBody;
  }

  public function put($route, array $args = [])
  {
    parent::put($route, $args); 
  }

  public function get($route)
  {
    $url = AFFILICON_SERVICE_URL . $route;

    $response = wp_remote_get($url, [
      'method' => 'GET',
      'headers' => parent::headers()
    ]);

    return $this->responseBody($response);
  }

  public function setUserName($username)
  {
    parent::setUserName($username); 
  }

  public function getUsername()
  {
    return parent::getUsername(); 
  }

  public function setPassword($password)
  {
    parent::setPassword($password); 
  }

  public function headers()
  {
    return parent::headers(); 
  }

  public function authenticate()
  {
    return parent::authenticate(); 
  }

  public function getToken()
  {
    return parent::getToken(); 
  }

  public function setClientId($id)
  {
    return parent::setClientId($id); 
  }

  public function getClientId()
  {
    return parent::getClientId(); 
  }

  public function getCountryId()
  {
    return parent::getCountryId(); 
  }

  public function setCountryId($countryId)
  {
    return parent::setCountryId($countryId); 
  }

  public function getUserLanguage()
  {
    return parent::getUserLanguage(); 
  }

  public function setUserLanguage($userLanguage)
  {
    return parent::setUserLanguage($userLanguage); 
  }

  public function isAuthenticated()
  {
    return parent::isAuthenticated();
  }

  /**
   * @return \Affilicon\Cart
   */
  public static function cart()
  {
    return parent::cart();
  }

}