<?php

/**
 * Class ItnsService
 */
class ItnsService
{
    /** @var AffiliconPaymentGateway */
    protected $gateway;

    /** @var  WC_Order */
    protected $wcOrder;

    /** @var \AffiliconApiClient\Client */
    protected $affiliconClient;

    /** @var  WP_REST_Request */
    private $request;

    /** @var object */
    private $requestData;

    /** @var AbstractTransaction */
    private $transaction;

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

            'callback' => function(WP_REST_Request $request) {

                $this->request = $request;

                if ($this->hasTransactionData()) {

                    $this->processTransaction();

                }

                // todo response message

            },

        ]);
    }

    /**
     * Gets the transaction type
     *
     * @return mixed
     */
    protected function getTransactionType()
    {
        return $this->requestData->type;
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
        $body = json_decode(
            $this->affiliconClient->decrypt($this->request->get_body())
        );

        if (empty($body->data)) {

            return null;

        }

        return $this->requestData = $body->data->transaction;
    }

    /**
     * @return boolean
     * @return $transaction
     */
    public function processTransaction()
    {

        $transactionModel = ucfirst($this->getTransactionType() . 'Transaction');

        if (class_exists($transactionModel)) {

            $this->transaction = new $transactionModel($this->requestData);

            $this->transaction->execute();

        }

    }
}