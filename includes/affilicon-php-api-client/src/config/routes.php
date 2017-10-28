<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        routes.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        24.10.17
 */

namespace Artsolution\AffiliconApiClient\Configurations;

if (!defined('API')) {
  define('API', [
    'routes' => [
      'auth' => [
        'anonymous' => '/auth/anonymous/token',
        'member' => '/auth/member/token',
      ],
      'products' => '/products',
      'refreshToken' => '/auth/refresh',
      'carts' => '/carts',
      'cartItemsProducts' => '/cart-items/products'
    ]
  ]);
}