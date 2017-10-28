<?php

/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 05.10.17
 * Time: 11:12
 */

namespace Artsolution\AffiliconApiClient\Interfaces;

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

  /**
   * @param $apiId
   * @return mixed
   */
  public function setApiId($apiId);

  /**
   * @return mixed
   */
  public function getApiId();

  /**
   * include specified components, e.g. variants or prices
   * @return Collection
   */
  public function with();


}