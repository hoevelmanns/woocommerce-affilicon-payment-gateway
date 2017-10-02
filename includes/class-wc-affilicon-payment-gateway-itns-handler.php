<?php

/**
 * Created by Marcelle Hövelmanns, art solution
 * Date: 14.09.16
 * Time: 22:41
 */


if (!defined('ABSPATH')) {
    exit;
}

class WC_Affilicon_Payment_Gateway_ITNS_Handler extends WC_Affilicon_Payment_Gateway_Response
{
    /** @var string Receiver email address to validate */
    protected $receiver_email;
    public $sandbox;
    public $query;

    public $gateway;


    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->itns_url = $gateway->get_option('affilicon_itns_url');
        $this->itns_secret = $gateway->get_option('affilicon_itns_secret');

        add_action('valid-affilicon-standard-itns-request', array($this, 'validResponse'));
    }

    /**
     * Check for affilicon ITNS Response.
     */
    public function checkResponse($query)
    {

        $itnsData = json_decode( file_get_contents( 'php://input' ), true );

        // check client, etc...
        if (!empty($itnsData)) {
            do_action('valid-affilicon-standard-itns-request', $itnsData);
            exit;
        }

        wp_die('affilicon ITNS Request Failure', 'affilicon ITNS', array('response' => 500));
    }

    /**x
     * There was a valid response.
     * @param  array $posted Post data after wp_unslash
     * @return bool
     */
    public function validResponse($posted)
    {
        $data = $posted['data'];

        if (!empty($data['transaction']) && ($order = $this->getAffiliconOrder($data['transaction']))) {
            // order exist

            $transactionType = strtolower($data['transaction']['type']);
            //@todo check transactionType for SALE -> first payment and important,  BILL for Recurring Payment (Abonnement)
            $paymentMethod = $data['transaction']['paymentMethod'];
            // @todo $verify = $posted['verify'];

            if (method_exists($this, 'paymentStatus_' . $transactionType)) {
                call_user_func(array($this, 'paymentStatus_' . $transactionType), $order);
            }

            return true;
        }
    }

    public function paymentStatus_sale($order)
    {
        // todo: Momentan wird Status "complete" anhand des "transaction" evaluiert -> Woocommerce-Plugin ITNS soll da einen eindeutigeren Status liefern

        $this->paymentComplete($order);

        // add affilicon customer-id to order
    }

    public function paymentStatus_bill($order)
    {
        // todo: process recurring payment
    }


}