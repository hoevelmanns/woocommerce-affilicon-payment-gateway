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

    $data = (object)$cart['data'];
    $this->id = $data->id;
    $this->status = $data->status;

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
   * @param AffiliconProduct $product
   * @return $this
   */
  public function addItem(AffiliconProduct $product)
  {
    // todo error handling
    $cartItem = $this->post(AFFILICON_API['routes']['cartItemsProducts'], [
      'cart_id' => $this->getId(),
      'product_id' => $product->getId(),
      'count' => $product->getQuantity()
    ]);

    $product->setApiId($cartItem['data']['id']);
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