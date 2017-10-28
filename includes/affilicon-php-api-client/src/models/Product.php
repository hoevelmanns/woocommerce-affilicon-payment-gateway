<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        ProductModel.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        23.10.17
 */

namespace Artsolution\AffiliconApiClient\Models;


use Artsolution\AffiliconApiClient\Abstracts\AbstractModel;

class Product extends AbstractModel
{

  public function __construct()
  {
    parent::__construct();
    $this->resource = API['routes']['products'];
  }

}