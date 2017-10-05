<?php

/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 05.10.17
 * Time: 11:12
 */

namespace AffiliconApi\Interfaces;

interface ProductInterface
{
  /**
   * @return mixed
   */
  public function getId();

  /**
   * @param $id
   * @return mixed
   */
  public function setId($id);

  /**
   * @param $quantity
   * @return mixed
   */
  public function setQuantity($quantity);

  /**
   * @return integer
   */
  public function getQuantity();

}