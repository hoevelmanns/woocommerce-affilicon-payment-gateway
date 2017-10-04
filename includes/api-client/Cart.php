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

  /**
   * create new cart
   * @return object
   */
  public function create()
  {

    $cart = $this->post(AFFILICON_ROUTES['carts'], [
      'vendor' => $this->gateway->vendor_id // todo dynamically
    ]);

    if (!$cart) {
      // todo exception
    }

    $this->cart = (object) $cart['data'];

    return $this;

  }

  /**
   * add product to cart
   * @param int $productId
   * @return $this
   */
  public function add($productId)
  {

    // todo error handling
    $cartItem = $this->post(AFFILICON_ROUTES['cartItemsProducts'], [
      'cart_id' => $this->cart->id,
      'product_id' => $productId
    ]);

    $this->cart->cart_items[] = $cartItem['data'];
    return $this;

  }

}