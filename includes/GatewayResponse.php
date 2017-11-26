<?php

/**
 * Created by Marcelle Hövelmanns, art solution
 * Date: 14.09.16
 * Time: 22:32
 */
class GatewayResponse
{

    /**
     * Get the order from the affilicon 'Custom' variable.
     * @return bool|WC_Order object
     */
    protected function getAffiliconOrder($transaction)
    {

        // data in correct format, so get the order.

        if (!empty($transaction['custom'])) {
            $custom = json_decode($transaction['custom'], true);
            $order_id = $custom['order_id'];
            $order_key = $custom['order_key'];//[1]; // todo: order key in ITNS-Request generieren und hier checken
        } else {
            // todo log WC_Gateway_Affilicon::log( 'Error: Order ID and key were not found in "custom".' );
            return false;
        }

        if (!$order = wc_get_order($order_id)) {
            // We have an invalid $order_id, probably because invoice_prefix has changed.
            $order_id = wc_get_order_id_by_order_key($order_key);
            $order = wc_get_order($order_id);
        }

        if ($order->order_key !== $order_key) {
            wp_die('Order not valid', 'affilicon ITNS', array('response' => 500));
            return false;
        }
        return $order;
    }

    protected function decrypt()
    {

    }

    /**
     * Complete order, add transaction ID and note.
     * @param  WC_Order $order
     * @param  string $txn_id
     * @param  string $note
     */
    protected function paymentComplete($order, $txn_id = '', $note = '')
    {
        $order->add_order_note($note);
        $order->payment_complete($txn_id);

        // todo: Speicherung der affilicon-Parameter "paymentMethod", "invoiceID", "customerId", etc
    }

}