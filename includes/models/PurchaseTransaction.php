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
class PurchaseTransaction extends AbstractTransaction
{
    public function execute()
    {
        $this->updateLineItemState();
        $this->updatePaymentState();
    }


    protected function updatePaymentState()
    {
        $orderLineItems = $this->wcOrder->get_items();
        $countPaidLineItems = 0;

        foreach ($orderLineItems as $orderLineItem) {
            $isPaid = (integer) getMetaDataValue($orderLineItem, 'affilicon_sale');
            if ($isPaid) {
                $countPaidLineItems++;
            }
        }

        if ($countPaidLineItems === count($orderLineItems)) {
            $this->paymentComplete();
        }
    }

    protected function paymentComplete()
    {
        $this->wcOrder->add_order_note('Payment method: ' . $this->transaction->getPaymentMethod());
        $this->wcOrder->payment_complete();
    }

}