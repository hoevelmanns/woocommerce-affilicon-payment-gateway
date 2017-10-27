<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        CartItem.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        05.10.17
 */

namespace Affilicon\ApiClient\Models;

use Affilicon\ApiClient\Abstracts\AbstractModel;
use Affilicon\ApiClient\Interfaces\ModelInterface;

/**
 * Class CartItem
 * @package Affilicon
 *
 * @property integer $id
 * @property integer $quantity
 * @property string $apiId;
 * @property string $name;
 * @property string $description;
 * @property integer $price
 */

class LineItem extends AbstractModel implements ModelInterface
{

  protected $resource;

  public function __construct()
  {
    parent::__construct();
    $this->resource = API['routes']['cartItemsProducts'];
  }

  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }

  /**
   * @param $quantity
   * @return $this
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
    return $this;
  }

  /**
   * @param $id
   * @return $this
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param $apiId
   * @return $this
   */
  public function setApiId($apiId)
  {
    $this->apiId = $apiId;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getApiId()
  {
    return $this->apiId;
  }

  public function fetch()
  {
    return parent::fetch();
  }


}