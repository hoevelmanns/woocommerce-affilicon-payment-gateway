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

/**
 * Class Cart
 * @package Affilicon
 *
 * @property string $id;
 * @property string $status
 *
 */

class Cart
{
  /** @var Collection $lineItems */
  private $lineItems;
  protected $resource;
  /** @var Client $client */
  protected $client;

  public function __construct()
  {
    $this->client = Client::getInstance();
    $this->lineItems = new Collection();
    $this->resource = AFFILICON_API['routes']['carts'];
  }

  /**
   * create new cart
   * @return object
   */
  public function create()
  {
    $cart = Request::getInstance()->post($this->resource,
      ['vendor' => $this->client->getClientId()],
      $this->client->headers()
    );

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
   * @param LineItem $item
   * @return $this
   */
  public function addLineItem(LineItem $item)
  {
    $lineItem = Request::getInstance()->post(AFFILICON_API['routes']['cartItemsProducts'], [
      'cart_id' => $this->getId(),
      'product_id' => $item->getId(),
      'count' => $item->getQuantity()
    ], $this->client->headers());

    $item->setApiId($lineItem->data->id);
    $this->lineItems->addItem($item);

    return $this;
  }

  /**
   * @param Collection $items
   */
  public function addLineItems(Collection $items)
  {
    while($items->next()) {
      if (!$items->current() instanceof LineItem) {
        continue;
      }

      $this->addLineItem($items->current());
    }
  }

  /**
   * get the cart items
   * @return mixed
   */
  public function getItems()
  {
    return $this->lineItems;
  }

}