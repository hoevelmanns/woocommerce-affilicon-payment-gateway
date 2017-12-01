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
 * Class SaleTransaction
 */
class SaleTransaction extends AbstractTransaction
{

    public function __construct($requestData)
    {
        parent::__construct($requestData);
    }

    /**
     * Processing the ITNS transaction
     */
    public function execute()
    {
        $this->orderLineItems = $this->order()->get_items();

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
        $this->order()->add_order_note('Payment method: ' . $this->payment()->getMethod());

        $this->order()->payment_complete();
    }

}