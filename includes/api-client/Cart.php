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
  /** @var  Collection $items */
  private $items;
  private $id;
  private $status;

  public function __construct()
  {
    parent::__construct();
    parent::authenticate();
    $this->items = new Collection();
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
   * @param CartItem $item
   * @return $this
   */
  public function addItem(CartItem $item)
  {
    // todo error handling
    $cartItem = $this->post(AFFILICON_API['routes']['cartItemsProducts'], [
      'cart_id' => $this->getId(),
      'product_id' => $item->getId(),
      'count' => $item->getQuantity()
    ]);

    $item->setApiId($cartItem->data->id);
    $this->items->addItem($item);

    return $this;
  }

  /**
   * @param Collection $items
   */
  public function addItems(Collection $items)
  {
    while($items->next()) {
      if (!$items->current() instanceof CartItem) {
        continue;
      }

      $this->addItem($items->current());
    }
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