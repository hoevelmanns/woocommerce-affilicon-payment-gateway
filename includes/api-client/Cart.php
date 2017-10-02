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

  public function __construct()
  {
    parent::__construct();
    parent::authenticate();

  }

  public function create()
  {

    $cart = $this->post(ROUTES['createCart'], [
      'headers'=> [

      ],
      'vendor' => '12345589034f' // todo dynamically
    ]);

    var_dump($cart);

    if (!$cart) {
      // todo exception
    }
  }

}