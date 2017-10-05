<?php

/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 04.10.17
 * Time: 12:32
 */
class WC_Affilicon_Payment_Gateway_Checkout_Form
{
  const ORDERFORM_URL_LEGACY = "https://secure.affilibank.de";
  const META_PREFIX = "affilicon";

  public $gateway;
  private $checkoutFormUrl = "";

  /** @var  WC_Order $order */
  private $order;

  /** @var  \AffiliconApi\AffiliconCart */
  public $cart;

  public function __construct(WC_Affilicon_Payment_Gateway $gateway, WC_Order $order)
  {

    $this->order = $order;
    $this->gateway = $gateway;

  }

  public function getMetaDataValue(WC_Product $product, $key)
  {
    foreach ($product->get_meta_data() as $meta) {
      if ($meta->key === $key) {
        return $meta->value;
      }
    }
    return false;
  }

  /**
   * get anonymous token and create new cart
   */
  public function buildCart()
  {

    /** @var \AffiliconApi\AffiliconCart $cart */
    $cart = (new \AffiliconApi\AffiliconCart())
        ->setClientId($this->gateway->vendor_id)
        ->create();

    $order = $this->order;
    $items = $order->get_items();

    /** @var WC_Order_Item $item */
    foreach ($items as $item) {

    /** @var WC_Product $product */
      $product = $item->get_product();

      $affiliconProductId = $this->getMetaDataValue($product, 'affilicon_product_id');

      /** @var \AffiliconApi\AffiliconProduct $affiliconProduct */
      $affiliconProduct = (new \AffiliconApi\AffiliconProduct())
          ->set($affiliconProductId, $item->get_quantity());

      $cart->addItem($affiliconProduct);

    }

    // todo build checkout form url
    $this->cart = $cart;

  }


  /**
   * build form parameter for legacy checkout form (Versions 2-3)
   * todo need refactoring
   *
   * @return string
   */
  public function buildLegacyFormUrl()
  {
    $this->legacyFormUrl($this->order);
  }

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

  public function buildLegacyWidgetFormUrl()
  {
    $prefill = $this->getAffiliconArgs($this->order);
    $clientId = $this->cart->getClientId();

    // todo: countryID = "de" or "de_DE"?
    $countryId = $this->getRegionCode($this->order->data['shipping']['country']); // todo check if needed "getRegionCode" or format = "de"
    $userLanguage = get_locale();

    // todo: language

    $params = [
      "$clientId/redirect",
      "cartId/{$this->cart->getId()}",
      "countryId/$countryId",
      "token/{$this->cart->getToken()}",
      //"language/$userLanguage", // todo core -> use case language
      //"prefill/$prefill" // todo core -> use case prefill
    ]; // todo testmode

    $this->checkoutFormUrl = self::ORDERFORM_URL_LEGACY . "/" . join('/', $params);

    var_dump($prefill);
  }

  /**
   * generate url for legacy checkout form with considering the prefill parameter
   * supported checkout forms 2 - 3 (without cart widget implementation)
   * @param $order
   * @return string
   */
  public function legacyFormUrl(WC_Order $order)
  {
    $orderFormUrl = self::ORDERFORM_URL_LEGACY; // todo option

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

    $orderFormUrl = "$orderFormUrl/$vendorId/$productTypeParam/product/$affiliconProductId/type/$paymentType/orderform_version/$orderFormTheme";

    $encryptedArgs = $this->getAffiliconArgs($order);

    $requestOrderformUrl = "$orderFormUrl?prefill=$encryptedArgs&custom=" . $order->get_id() . "|" . $order->order_key;

    //var_dump($product->is_type('job_package'));
    //var_dump($product->get_type());
    //die();
    // todo: logging: WC_Affilicon_Payment_Gateway::log( 'Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url );

    $this->checkoutFormUrl = $requestOrderformUrl;
  }


  private function crypt($data)
  {
    $cryptPass = $this->gateway->itns_secret_key;
    $cryptMethod = 'blowfish';
    return urlencode(openssl_encrypt($data, $cryptMethod, $cryptPass));
  }

  public function getAffiliconArgs($order)
  {
    $args = [
      'currency' => get_woocommerce_currency(),

      // todo: order_id und order_key in itns-response berücksichtigen !!!!!!!!!!!!!!!!!!!!!!!!!!
      'custom' => json_encode([
        'order_id' => $order->id,
        'order_key' => $order->order_key,
      ]),

      //'custom' => $order->id . '|' . $order->order_key, // @todo wird in prefillAction Orderform nicht berücksichtigt
      'basic_addr_firstname' => $order->billing_first_name,
      'basic_addr_lastname' => $order->billing_last_name,
      'basic_addr_email' => $order->billing_email,
      'basic_addr_phone' => $order->billing_phone,

      'billing_addr_company' => $order->billing_company,
      'billing_addr_firstname' => $order->billing_first_name,
      'billing_addr_lastname' => $order->billing_last_name,
      'billing_addr_street' => $order->billing_address_1,
      'billing_addr_street2' => $order->billing_address_2,
      'billing_addr_city' => $order->billing_city,
      'billing_addr_zip' => $order->billing_postcode,
      'billing_addr_country' => $order->billing_country,
      //todo Hash generieren und von ITNS-Response zurückliefern lassen und checken!
    ];

    if ($this->gateway->testmode) {
      $args['testmode'] = 'true';
    }

    // encode json and encrypt
    return $this->crypt(json_encode($args, true));
  }
}