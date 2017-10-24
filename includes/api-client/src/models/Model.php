<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Model.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        22.10.17
 */

namespace Affilicon;


abstract class Model extends Client
{

  private $resource;

  public function construct()
  {
    parent::__construct();
  }

  /**
   * Find item by id
   * @param $id
   * @return null
   */
  public function findById($id)
  {
    return $this->get("{$this->resource}/$id");
  }

  public function findOne()
  {

  }
}