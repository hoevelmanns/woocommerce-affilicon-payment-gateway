<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Order.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        01.12.17
 */


class Order extends WC_Order
{
    /**
     * Order constructor.
     * @param int $orderId
     */
    public function __construct($orderId = null)
    {
        parent::__construct($orderId);
    }

}