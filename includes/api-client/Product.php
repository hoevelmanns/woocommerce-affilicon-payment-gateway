<?php

/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 05.10.17
 * Time: 11:11
 */

namespace AffiliconApi;

use AffiliconApi\Interfaces\ProductInterface;

class AffiliconProduct extends AffiliconApi implements ProductInterface
{

  private $id;
  private $quantity;
  private $apiId;

  public function __construct()
  {
    parent::__construct();
    parent::authenticate();
  }

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

}