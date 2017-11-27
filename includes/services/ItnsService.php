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
 * Class ItnsService
 */
class ItnsService
{
    /** @var AffiliconPaymentGateway */
    protected $gateway;

    /** @var  WP_REST_Request */
    protected $request;

    /** @var object */
    protected $requestData;

    /** @var AbstractTransaction */
    public $transaction;

    /** @var  WC_Order */
    protected $wcOrder;

    /** @var \AffiliconApiClient\Client */
    protected $affiliconClient;

    const REFUND = 'refund';
    const CHARGEBACK = 'chargeback';
    const SALE = 'sale';

    public function __construct(\AffiliconApiClient\Client $client)
    {
        $this->affiliconClient = $client;

        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * Registers the route
     * @return void
     */
    public function registerRoutes()
    {
        register_rest_route(AFFILICON_REST_BASE_URI, AFFILICON_REST_TRANSACTION_ROUTE, [
            'methods' => 'POST',
            'callback' => [$this, 'checkItnsRequest'],
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return bool
     */
    public function checkItnsRequest($request)
    {
        $this->request = $request;

        if ($this->hasTransactionData()) {

            $this->hasValidTransactionData();

        }
        // todo json message
    }

    protected function getTransactionType()
    {
        $type = $this->requestData->type;

        if (!$type) {
            exit;
        }

        return $type;
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
        $body = json_decode($this->affiliconClient->decrypt($this->request->get_body()));

        if (empty($body->data)) {
            return null;
        }

        return $this->requestData = $body->data->transaction;
    }

    /**
     * There was a valid response.
     * @return boolean
     */
    public function hasValidTransactionData()
    {

        switch ($this->getTransactionType()) {
            case 'sale': {

                $this->transaction = (new ChargebackTransaction())
                    ->execute();

                break;
            }

            case 'refund': {
                //$this->transaction = new RefundTransaction();
                break;
            }

            case 'chargeback': {

                break;
            }
            default: {
                exit;
            }
        }

        $this->transaction->set($this->requestData);
    }
}