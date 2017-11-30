<?php

/**
 * Created by Marcelle Hövelmanns, art solution
 * Date: 14.09.16
 * Time: 22:41
 */

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

    /** @var string */
    const REFUND = 'refund';
    /** @var string */
    const CHARGEBACK = 'chargeback';
    /** @var string */
    const SALE = 'sale';

    /**
     * ItnsService constructor.
     * @param \AffiliconApiClient\Client $client
     */
    public function __construct(\AffiliconApiClient\Client $client)
    {
        $this->affiliconClient = $client;

        add_action('rest_api_init', [$this, 'registerRoute']);
    }

    /**
     * Registers the route
     * @return void
     */
    public function registerRoute()
    {
        register_rest_route(AFFILICON_REST_BASE_URI, AFFILICON_REST_TRANSACTION_ROUTE, [
            'methods' => 'POST',
            'callback' => [$this,

            /** WP_REST_Request */
            function(WP_REST_Request $request) {
                $this->request = $request;

                if ($this->hasTransactionData()) {

                    $this->processTransaction();

                }
            }],

        ]);
    }

    /**
     * Gets the transaction type
     *
     * @return mixed
     */
    protected function getTransactionType()
    {
        $type = $this->requestData->type;

        if (!$type) {
            exit;
        }

        return $type;
    }

    /**
     * Checks for data in the request body
     *
     * @return bool
     */
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
     * @return boolean
     */
    public function processTransaction()
    {
        switch ($this->getTransactionType()) {

            case 'sale': {

                $this->transaction = new PurchaseTransaction();

                break;
            }

            case 'refund': {
                $this->transaction = new RefundTransaction();
                break;
            }

            case 'chargeback': {
                $this->transaction = new ChargebackTransaction();
                break;
            }
            default: {
                exit; // todo response message
            }

        }

        $this->transaction
            ->set($this->requestData)
            ->execute();
    }
}