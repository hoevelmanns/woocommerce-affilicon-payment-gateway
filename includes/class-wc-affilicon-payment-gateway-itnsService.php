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
class WC_Affilicon_Payment_Gateway_ItnsService
{
    /** @var WC_Affilicon_Payment_Gateway */
    protected $gateway;

    /** @var  WP_REST_Request */
    protected $request;

    protected $transactionData;

    protected $affiliconClient;

    public function __construct(\AffiliconApiClient\Client $client)
    {
        $this->affiliconClient = $client;

        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes()
    {
        register_rest_route( 'affilicon/v1', 'transaction', [
            'methods' => 'POST',
            'callback' => [$this, 'checkItnsRequest'],
        ]);
    }

    public function checkItnsRequest(WP_REST_Request $request)
    {
        $this->request = $request;

        if ($this->hasTransactionData()) {

            $this->hasValidTransaction();

        }

        return false;
    }

    protected function hasTransactionData()
    {
        return !empty($this->getTransactionData());
    }

    /**
     * Check for affilicon ITNS Response.
     */
    public function getTransactionData()
    {
        $transaction = $this->affiliconClient->decrypt($this->request->get_body());

        $this->transactionData = $transaction;

        return $transaction;
    }

    /**x
     * There was a valid response.
     * @return void
     */
    public function hasValidTransaction()
    {

        return;
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