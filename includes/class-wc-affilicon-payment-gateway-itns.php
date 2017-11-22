<?php

/**
 * Created by Marcelle Hövelmanns, art solution
 * Date: 14.09.16
 * Time: 22:41
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WC_Affilicon_Payment_Gateway_Itns
 */
class WC_Affilicon_Payment_Gateway_Itns
{
    /** @var WC_Affilicon_Payment_Gateway */
    protected $gateway;

    /** @var  WP_REST_Request */
    protected $request;

    protected $transaction;

    protected $affiliconClient;

    public function __construct(\AffiliconApiClient\Client $client)
    {
        //$this->gateway = $gateway;
        $this->affiliconClient = $client;
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes()
    {
        register_rest_route( 'affilicon/v1', 'transaction', array(
            'methods' => 'POST',
            'callback' => [$this, 'checkResponse'],
        ) );
    }

    protected function checkResponse(WP_REST_Request $request)
    {
        $this->request = $request;
        if (!$this->hasTransactionData()) {
            return false; // todo no response handling
        }
    }

    protected function hasTransactionData()
    {
        return $this->getTransactionData();
    }


    /**
     * Check for affilicon ITNS Response.
     */
    public function getTransactionData()
    {
        $transaction = $this->decrypt($this->request->get_body());

        // check client, etc...
        if (!empty($itnsData)) {
            do_action('valid-affilicon-standard-itns-request', $itnsData);
            exit;
        }

        wp_die('affilicon ITNS Request Failure', 'affilicon ITNS', array('response' => 500));
    }

    protected function decrypt($body)
    {
        $secretKey = $this->gateway->itns_secret_key;

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

       // $this->paymentComplete($order);

        // add affilicon customer-id to order
    }

    public function paymentStatus_bill($order)
    {
        // todo: process recurring payment
    }


}