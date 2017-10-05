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

  public $clientId;
  public $cart;

  public function __construct()
  {
    parent::__construct();
    parent::authenticate();

  }

  public function setClientId($id)
  {
      $this->clientId = $id;
      return $this;
  }

  public function getClientId()
  {
      return $this->clientId;
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
  public function add(AffiliconProduct $product)
  {

    // todo error handling
    $cartItem = $this->post(AFFILICON_ROUTES['cartItemsProducts'], [
      'cart_id' => $this->getId(),
      'product_id' => $product->getId(),
      'count' => $product->getQuantity()
    ]);

    $this->cart->cart_items[] = $cartItem['data'];
    return $this;

  }

}

/**
private function productWithMetaData(WC_Order_Item $item)
{
    $product = $item->get_product();

    foreach ($product->get_meta_data() as $meta) {
        if (preg_match(self::META_PREFIX,$meta->key)) {
            $product[$meta->key] = $meta->value;
        }
    }
    return $product;
}**/