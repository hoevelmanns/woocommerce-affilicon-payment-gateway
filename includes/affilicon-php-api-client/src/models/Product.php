<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        ProductModel.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        23.10.17
 */

namespace AffiliconApiClient\Models;


use AffiliconApiClient\Abstracts\AbstractModel;

class Product extends AbstractModel
{

  protected $route;

  public function __construct()
  {
    parent::__construct();
  }

}