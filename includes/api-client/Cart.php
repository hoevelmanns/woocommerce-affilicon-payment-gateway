<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Cart.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace AffiliconApi;

class AffiliconCart extends AffiliconApi
{

  private $clientId;
  private $items;

  public function __construct()
  {
    parent::__construct();
    parent::authenticate();

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

  /**
   * get the cart items
   * @return mixed
   */
  public function getItems()
  {
    return $this->items;
  }

  /**
   * create new cart
   * @return object
   */
  public function create()
  {

    $cart = $this->post(AFFILICON_ROUTES['carts'], [
      'vendor' => $this->clientId
    ]);

    if (!$cart) {
      // todo exception
    }

    $this->cart = (object) $cart['data'];

    return $this;

  }

  public function getId()
  {
      return $this->cart->id;
  }

    /**
     * @param AffiliconProduct $product
     * @return $this
     */
  public function addItem(AffiliconProduct $product)
  {

    // todo error handling
    $cartItem = $this->post(AFFILICON_ROUTES['cartItemsProducts'], [
      'cart_id' => $this->getId(),
      'product_id' => $product->getId(),
      'count' => $product->getQuantity()
    ]);

    $this->items[] = $cartItem['data'];

    return $this;

  }

}