<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Model.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        22.10.17
 */

namespace AffiliconApiClient\Abstracts;


use AffiliconApiClient\Client;
use AffiliconApiClient\Interfaces\ModelInterface;
use AffiliconApiClient\Services\HttpService;

abstract class AbstractModel implements ModelInterface
{

  protected $resource;
  /** @var HttpService */
  protected $HttpService;
  /** @var Client  */
  protected $Client;
  protected $rows;

  public function __construct()
  {
    $this->HttpService = HttpService::getInstance();
    $this->Client = Client::getInstance();
  }

  public function findById($id)
  {
    // TODO: Implement findById() method.
  }

  public function find($params, $with)
  {
    // TODO: Implement find() method.
  }

  public function fetch()
  {
    // TODO: Implement all() method.
  }

}