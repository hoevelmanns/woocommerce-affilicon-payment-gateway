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
 * Class PurchaseTransaction
 */
class PurchaseTransaction extends AbstractTransaction
{
    /**
     * Processing the ITNS transaction
     */
    public function execute()
    {
        $this->orderLineItems = $this->wcOrder->get_items();

        $this->updateLineItemStates();

        if ($this->lineItemsFulfilled()) {

            $this->paymentComplete();

        }

        // todo return result
    }

    /**
     * Updates the payment state of the woocommerce current line item order and
     * sets order to complete if all line item from order are successfully processed.
     *
     */
    protected function paymentComplete()
    {
        $this->wcOrder->add_order_note('Payment method: ' . $this->getPaymentMethod());

        $this->wcOrder->payment_complete();
    }

}