<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        ItnsTransaction.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        23.11.17
 */

/**
 * Class Transaction
 */
class ChargebackTransaction extends AbstractTransaction
{
    public function execute()
    {
        $this->updateLineItemState();
        $this->updateOrderState();
    }

    protected function updateOrderState()
    {
        // todo complete refund
        wc_order_refund($this->getWcOrderId());
    }

}