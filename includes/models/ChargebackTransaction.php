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
 * Class ChargebackTransaction
 */
class ChargebackTransaction extends AbstractTransaction
{
    public function execute()
    {
        $this->updateLineItemStates();
        $this->updateOrderState();

    }

    protected function updateOrderState()
    {
        // todo complete refund, line items also possible to refund
        /**
         * $default_args = array(
        'amount'         => 0,
        'reason'         => null,
        'order_id'       => 0,
        'refund_id'      => 0,
        'line_items'     => array(),
        'refund_payment' => false,
        'restock_items'  => false,
        );
         */
       // wc_create_refund($this->getWcOrderId());
    }

}