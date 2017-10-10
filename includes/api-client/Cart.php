<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Cart.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        02.10.17
 */

namespace Affilicon;

class Cart extends ApiClient
{
  private $items;
  private $id;
  private $status;

  public function __construct()
  {
    parent::__construct();
    parent::authenticate();
  }

  /**
   * create new cart
   * @return object
   */
  public function create()
  {
    $cart = $this->post(AFFILICON_API['routes']['carts'], [
      'vendor' => $this->getClientId()
    ]);

    if (!$cart) {
      // todo exception handling
    }

    $this->id = $cart->data->id;
    $this->status = $cart->data->status;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param Product $product
   * @return $this
   */
  public function addItem(Product $product)
  {
    // todo error handling
    $cartItem = $this->post(AFFILICON_API['routes']['cartItemsProducts'], [
      'cart_id' => $this->getId(),
      'product_id' => $product->getId(),
      'count' => $product->getQuantity()
    ]);

    $product->setApiId($cartItem->data->id);
    $this->items[] = $product;

    return $this;
  }

  /**
   * get the cart items
   * @return mixed
   */
  public function getItems()
  {
    return $this->items;
  }
}