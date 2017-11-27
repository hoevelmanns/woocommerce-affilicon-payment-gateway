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

    /** @var Transaction */
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
        $this->transaction = (new Transaction())
            ->set($this->requestData);

        $this->wcOrder = wc_get_order($this->transaction->getWcOrderId());

        $this->updateLineItemState();

        $this->updatePaymentState();
    }

    /**
     * Sets the item state of the product from requested transaction
     *
     */
    public function updateLineItemState()
    {
        $wcLineItems = $this->wcOrder->get_items();

        /** @var WC_Order_Item $item */
        foreach ($wcLineItems as $item) {

            /** @var WC_Order_Item_Product $product */
            $orderItemProduct = $item->get_product();

            $itemProductId = getMetaDataValue($orderItemProduct, 'affilicon_product_id'); // todo const affilicon_product_id

            if ($itemProductId === $this->transaction->getProductId()) {
                $this->applyState($item);
            }
        }
    }

    /**
     * @param WC_Order_Item $item $item
     */
    protected function applyState($item)
    {
        $transactionType = $this->transaction->getType();

        switch ($transactionType) {

            case self::REFUND: {
                // todo refund for single entry possible?
                wc_create_refund($this->transaction->getWcOrderId());
                break;
            }

            case self::CHARGEBACK: {
                // todo chargeback case

            }

        }

        $metaKey = "affilicon_$transactionType";

        if (empty(getMetaDataValue($item, $metaKey))) {

            $item->add_meta_data($metaKey, 1);
            $item->save();

        }
    }

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

    protected function paymentComplete()
    {
        $this->wcOrder->add_order_note('Payment method: ' . $this->transaction->getPaymentMethod());
        $this->wcOrder->payment_complete();
    }

}