<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        routes.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        24.10.17
 */

namespace AffiliconApiClient\Configurations;

return $routes = [

  /*
   * ---------------------------------------------------------------------
   * Routes Configurations
   * ---------------------------------------------------------------------
   */

  'routes' => [

    'auth' => [

      'anonymous' => '/auth/anonymous/token',

      'member' => '/auth/member/token',

      'refresh' => '/auth/refresh',

    ],

    // Models
    'Product' => '/products',

    'Cart' => '/carts',

    'LineItem' => '/cart-items/products'
  ]
];