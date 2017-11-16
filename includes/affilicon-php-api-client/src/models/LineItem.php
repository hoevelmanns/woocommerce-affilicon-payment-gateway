<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        CartItem.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        05.10.17
 */

namespace AffiliconApiClient\Models;

use AffiliconApiClient\Abstracts\AbstractModel;
use AffiliconApiClient\Interfaces\ModelInterface;

/**
 * Class CartItem
 * @package Affilicon
 *
 * @property integer $id
 * @property string $cartId
 * @property integer $quantity
 * @property string $apiId
 * @property string $name
 * @property string $description
 * @property integer $price
 */
class LineItem extends AbstractModel implements ModelInterface
{
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

    /**
     * @param $cartId
     * @return $this
     */
    public function setCartId($cartId)
    {
        $this->cartId = $cartId;
        return $this;
    }

    /**
     * @return $this
     */
    public function store()
    {
        $data = $this->post([
            'cart_id' => $this->cartId,
            'product_id' => $this->id,
            'count' => $this->quantity])->data();

        $this->setApiId($data->id);

        return $this;
    }


}