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
     * @return integer
     */
    public function getQuantity();

    /**
     * @param $id
     * @param $quantity
     * @return mixed
     */
    public function set($id, $quantity);
}