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
    /**
     * Processing the ITNS transaction
     */
    public function execute()
    {
        $this->updateLineItemState();
        $this->updatePaymentState();

        // todo return result
    }

    /**
     * Updates the payment state of the woocommerce current line item order and
     * sets order to complete if all line item from order are successfully processed.
     *
     * @return void
     */
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

    /**
     * Sets the woocommerce order state to complete
     */
    protected function paymentComplete()
    {
        $this->wcOrder->add_order_note('Payment method: ' . $this->getPaymentMethod());
        $this->wcOrder->payment_complete();
    }

}