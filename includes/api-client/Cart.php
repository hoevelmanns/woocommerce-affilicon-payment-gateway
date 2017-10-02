<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Cart.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

class AffiliconCart extends AffiliconApi
{

  public $gateway;
  private $cart;

  public function __construct(WC_Affilicon_Payment_Gateway $gateway)
  {
    $this->gateway = $gateway;
    parent::__construct($gateway);
    parent::authenticate();

  }

  public function create()
  {

    $cart = $this->post('/carts', [
      'vendor' => $this->gateway->vendor_id // todo dynamically
    ]);

    var_dump($cart);

    if (!$cart) {
      // todo exception
    }

    $this->cart = (object) $cart['data'];

    return $this->cart;
  }

  /**
   * add product to cart
   * @param int $productId
   */
  public function add(int $productId)
  {
    $route = '/cart-items/products';

    $store = $this->post($route, [
      'cart_id' => $this->cart->id,
      'product_id' => $productId
    ]);

    var_dump($store);

  }

}