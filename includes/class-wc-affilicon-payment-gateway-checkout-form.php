<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Cart.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * Date: 04.10.17
 */

/**
 * Class WC_Affilicon_Payment_Gateway_Checkout_Form
 */
class WC_Affilicon_Payment_Gateway_Checkout_Form
{

  /** @var WC_Affilicon_Payment_Gateway $gateway */
  public $gateway;
  private $checkoutFormUrl;

  /** @var  WC_Order $order */
  private $order;

  /** @var \AffiliconApiClient\Client */
  private $affiliconClient;

  public function __construct(WC_Affilicon_Payment_Gateway $gateway, WC_Order $order)
  {
    $this->affiliconClient = \AffiliconApiClient\Client::getInstance();
    $this->affiliconClient
      ->setEnvironment('staging')
      ->setCountryId('de') // todo get from woocommerce
      ->setUserLanguage('de_DE') // todo get from wordpress/woocommerce
      ->setClientId($gateway->vendor_id)
      ->init();

    $this->order = $order;
    $this->gateway = $gateway;
  }

  /**
   * @param $item
   * @param $key
   * @return bool
   */
  public function getMetaDataValue($item, $key)
  {
    foreach ($item->get_meta_data() as $meta) {
      if ($meta->key === $key) {
        return $meta->value;
      }
    }
    return false;
  }

  public function address($type)
  {
    $address = [
      $type.'_addr_company' => call_user_func([ $this->order, "get_{$type}_company" ]),
      $type.'_addr_firstname' => call_user_func([ $this->order, "get_{$type}_first_name" ]),
      $type.'_addr_lastname' => call_user_func([ $this->order, "get_{$type}_last_name" ]),
      $type.'_addr_street' => call_user_func([ $this->order, "get_{$type}_address_1" ]),
      $type.'_addr_street2' => call_user_func([ $this->order, "get_{$type}_address_2" ]),
      $type.'_addr_city' => call_user_func([ $this->order, "get_{$type}_city" ]),
      $type.'_addr_zip' => call_user_func([ $this->order, "get_{$type}_postcode" ]),
      $type.'_addr_country' => call_user_func([ $this->order, "get_{$type}_country" ]),
    ];

    return $address;
  }

  public function basicAddress()
  {
    return $this->address('basic');
  }

  public function billingAddress()
  {
    return $this->address('billing');
  }

  public function shippingAddress()
  {
    return $this->address('shipping');
  }

  /**
   * Creates a new cart and passes the Woocommerce cart items.
   *
   * @return \AffiliconApiClient\Models\Cart
   */
  public function buildCart()
  {
    $affiliconCart = (new \AffiliconApiClient\Models\Cart())->create();

    $this->order->add_meta_data('affilicon_cart_id', $affiliconCart->getId());

    /** @var WC_Order_Item $wcLineItem */
    foreach ($this->order->get_items() as $wcLineItem) {

      $affiliconProductId = $this->getMetaDataValue($wcLineItem->get_product(), 'affilicon_product_id');

      if ($affiliconProductId) {

        $affiliconCart->addLineItem($affiliconProductId, $wcLineItem->get_quantity());

      }

    }

    $this->order->save();

    return $affiliconCart;
  }

  /**
   * build form parameter for legacy checkout form (Versions 2-3)
   * todo need refactoring
   *
   * @return string|void
   */
  public function buildLegacyFormUrl()
  {
    $this->legacyFormUrl($this->order);
  }

  /**
   * @return mixed
   */
  public function getUrl()
  {
    return $this->checkoutFormUrl;
  }

  /**
   * build url for new checkout form
   */
  public function buildCheckoutUrl()
  {

  }

  /**
   * @param $code
   * @return mixed
   */
  public function getRegionCode($code)
  {
    $code = strtolower($code);

    $mapper = [
      'us' => 'en_US',
      'de' => 'de_DE',
      'it' => 'it_IT',
      'es' => 'es_ES',
      'fr' => 'fr_FR',
    ];

    return $mapper[$code];
  }

  /**
   * @param \AffiliconApiClient\Models\Cart $cart
   */
  public function buildLegacyWidgetFormUrl($cart)
  {
    $prefill = $this->getAffiliconArgs($this->order);
    $clientId = $this->affiliconClient->getClientId();

    // todo: countryID = "de" or "de_DE"?
    $countryId = $this->getRegionCode($this->order->data['shipping']['country']); // todo check if needed "getRegionCode" or format = "de"
    $userLanguage = $this->affiliconClient->getUserLanguage();

    // todo: language

    $params = [
      "$clientId/redirect",
      "cartId/{$cart->getId()}",
      "countryId/$countryId",
      "token/{$this->affiliconClient->getToken()}",
      "language/$userLanguage" // todo core -> use case language
    ]; // todo testmode

    $this->checkoutFormUrl = AFFILICON_CHECKOUT_FORM_URL_LEGACY . "/" . join('/', $params) . "?prefill=$prefill";

    var_dump($prefill);
  }

  /**
   * Generate url for legacy checkout form with considering the prefill parameter
   * supported checkout forms 2 - 3 (without cart widget implementation)
   * @param $order
   * @return string
   */
  public function legacyFormUrl(WC_Order $order)
  {
    $vendorId = $this->gateway->vendor_id;
    $customer = new WC_Customer($order->get_id());
    $orderData = $order->get_items();
    $productData = reset($orderData);
    $settings = $this->gateway->settings;

    $paymentType = isset($settings['testmode']) ? "tst" : 'elv'; // todo -> if selected option Testorder -> "tst";
    $orderFormTheme = isset($settings['affilicon_checkout_form_theme']) ? $settings['affilicon_checkout_form_theme'] : 3;

    if (!$productData) {
      return; // todo: vernünftige Behandlung
    }

    $productId = $productData['product_id'];
    $product = new WC_Product($productId);

    // todo later get product type by woocommerce product type
    $productType = $product->get_attribute('Affilicon-Produkt-Typ');
    $productTypeParam = $productType ?: 'start'; // 1 = Standard-Produkt

    $affiliconProductId = $product->get_attribute('Affilicon-Produkt-ID'); // todo: Attribute code-technisch erweitern

    $orderFormUrl = AFFILICON_CHECKOUT_FORM_URL_LEGACY."/$vendorId/$productTypeParam/product/$affiliconProductId/type/$paymentType/orderform_version/$orderFormTheme";

    $encryptedArgs = $this->getAffiliconArgs($order);

    $requestOrderformUrl = "$orderFormUrl?prefill=$encryptedArgs&custom=" . $order->get_id() . "|" . $order->order_key;

    //var_dump($product->is_type('job_package'));
    //var_dump($product->get_type());
    //die();
    // todo: logging: WC_Affilicon_Payment_Gateway::log( 'Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url );

    $this->checkoutFormUrl = $requestOrderformUrl;
  }

  /**
   * @param $data
   * @return string
   */
  private function crypt($data)
  {
    $cryptPass = $this->gateway->itns_secret_key;
    $cryptMethod = 'blowfish';
    return urlencode(openssl_encrypt($data, $cryptMethod, $cryptPass));
  }

  /**
   * @param $order
   * @return string
   */
  public function getAffiliconArgs($order)
  {
    $args = [
      'currency' => get_woocommerce_currency(),
      'custom' => json_encode([
        'order_id' => $order->id,
        'order_key' => $order->order_key,
      ]),
    ];

    $args = array_merge(
      $args,
      $this->shippingAddress(),
      $this->billingAddress(),
      $this->basicAddress()
    );

    if ($this->gateway->testmode) {
      $args['testmode'] = 'true';
    }

    // encode json and encrypt
    return $this->crypt(json_encode($args, true));
  }
}