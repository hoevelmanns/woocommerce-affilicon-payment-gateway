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

    public function __construct()
    {
        parent::__construct();
        parent::authenticate();
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getId() {
        return $this->id;
    }

    public function create($productId, $quantity)
    {
        $this->id = $productId;
        $this->quantity = $quantity;
        return $this;
    }
}