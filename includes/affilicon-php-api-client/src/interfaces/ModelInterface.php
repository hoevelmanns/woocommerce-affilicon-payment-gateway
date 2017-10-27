<?php
/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 26.10.17
 * Time: 14:17
 */

namespace Affilicon\ApiClient\Interfaces;


interface ModelInterface
{
  /**
   * Find item by his id
   *
   * @param $id
   * @return \stdClass
   */
  public function findById($id);

  /**
   * Find items by different parameters
   *
   * @param $params
   * @param $with
   * @return mixed
   */
  public function find($params, $with);

  /**
   * Gets all records
   * @return mixed
   */
  public function fetch();
}