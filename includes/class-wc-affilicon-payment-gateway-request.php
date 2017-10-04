<?php

/**
 * Created by Marcelle Hövelmanns, art solution
 * Date: 09.09.16
 * Time: 21:04
 */


if (!defined('ABSPATH')) {
  exit;
}

class WC_Affilicon_Payment_Gateway_Request
{

  const ORDERFORM_URL = "https://secure.affilibank.de";

  public $gateway;
  private $checkoutFormUrl = "";

  /** @var  WC_Order $order */
  private $order;

  public function __construct(WC_Affilicon_Payment_Gateway $gateway, WC_Order $order)
  {

    $this->order = $order;
    $this->gateway = $gateway;

  }

  public function getMetaDataByKey(WC_Product $product, $key)
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
  public function prepareCheckoutForm()
  {
    /** @var AffiliconCart $cart */
    $cart = (new AffiliconCart($this->gateway))->create();

    $order = $this->order;
    $cartItems = $order->get_items();

    /** @var WC_Order_Item $item */
    foreach ($cartItems as $item) {

      /** @var WC_Product $product */
      $product = $item->get_product();
      $affiliconProductId = (int) $this->getMetaDataByKey($product, 'affilicon_product_id');

      if (!$affiliconProductId) {
        continue;
      }

      $cart->add($affiliconProductId);
    }

    // todo build checkout form url
    var_dump($cart);

  }


  /**
   * build form parameter for legacy checkout form (Versions 2-3)
   * todo need refactoring
   *
   * @return string
   */
  public function prepareLegacyForm()
  {
    $this->legacyFormUrl($this->order);
  }

  public function getCheckoutFormUrl()
  {
    return $this->checkoutFormUrl;
  }

  /**
   * generate url for legacy checkout form with considering the prefill parameter
   * supported checkout forms 2 - 3 (without cart widget implementation)
   * @param $order
   * @return string
   */
  public function legacyFormUrl(WC_Order $order)
  {
    $orderFormUrl = self::ORDERFORM_URL; // todo option

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

    $args = $this->getAffiliconArgs($order);
    $jsonArgs = json_encode($args, true);
    $encryptedArgs = $this->crypt($jsonArgs);

    $requestOrderformUrl = "$orderFormUrl?prefill=$encryptedArgs&custom=" . $order->id . "|" . $order->order_key;

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

    return $args;
  }
}