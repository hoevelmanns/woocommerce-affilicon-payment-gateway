<?php

/**
 * Class OrderService
 */
class OrderService
{
    /** @var AffiliconPaymentGateway $gateway */
    public $gateway;

    /** @var string */
    protected $checkoutUrl;

    /** @var WC_Order */
    protected $wcOrder;

    /** @var \AffiliconApiClient\Models\Order */
    private $apiOrder;

    use Address;

    /**
     * CheckoutService constructor.
     * @param WC_Order $wcOrder
     */
    public function __construct(WC_Order $wcOrder)
    {
        $this->wcOrder = $wcOrder;
    }

    /**
     * Creates a new cart and passes the Woocommerce cart items.
     * @throws \AffiliconApiClient\Exceptions\ConfigurationInvalid
     */
    public function createOrderWithCart()
    {
        $this->createOrder();

        $this->generateCart();

    }

    /**
     * Creates a new order for the legacy checkout form
     * @throws \AffiliconApiClient\Exceptions\ConfigurationInvalid
     */
    public function createOrder()
    {
        $this->apiOrder = new \AffiliconApiClient\Models\Order();

        $this->addItnsCallback();

        $this->addBillingData();

        $this->addShippingData();

        $this->addBasicAddressData();

        $this->checkoutUrl = $this->getCheckoutUrl();
    }

    /**
     * Adds the hook registering data to the custom field,
     * in this case, an ITNS connection called Woocommerce Payment Gateway
     *
     * @return void
     */
    protected function addItnsCallback()
    {
        $this->apiOrder
            ->setCallbackUrl($this->getTransactionEndpoint())
            ->setCallbackItnsTypeId('15'); // todo define // Woocommerce Payment Gateway

        $this->apiOrder->addCallbackData([
            'data' => [
                'wc_order_id' => $this->wcOrder->get_id(),
                'wc_order_key' => $this->wcOrder->get_order_key(),
            ]
        ]);
    }

    /**
     * Get the transaction endpoint
     * @return string
     */
    public function getTransactionEndpoint()
    {
        return get_rest_url() .
            AFFILICON_REST_BASE_URI . '/' .
            AFFILICON_REST_TRANSACTION_ROUTE;
    }

    /**
     * Returns the generated checkout url
     *
     * @return string
     * @throws \AffiliconApiClient\Exceptions\ConfigurationInvalid
     */
    public function getCheckoutUrl()
    {
        return $this->apiOrder->getCheckoutUrl();
    }

    public function getCheckoutUrlLegacy()
    {
        /** @var \AffiliconApiClient\Client $client */
        $client = \AffiliconApiClient\Client::getInstance();

        $env = $client->getEnv();

        $prefillData = json_encode($this->apiOrder->collectPrefillData());

        $encryptedPrefillData = urlencode($client->encrypt($prefillData));

        /** @var WC_Product $product */
        $product = reset($this->wcOrder->get_items())->get_product();

        $affiliconProductId = getMetaDataValue($product, 'affilicon_product_id');

        $isSubscriptionProduct = getMetaDataValue($product, 'affilicon_product_is_subscription');

        $urlPath = $isSubscriptionProduct ? 'abo' : 'index';

        return $env->secure_url . "/{$client->getClientId()}/$urlPath?product=$affiliconProductId&language={$client->getUserLanguage()}&prefill=$encryptedPrefillData";

    }

    /**
     * Generates the affilicon cart
     */
    public function generateCart()
    {
        $cart = $this->apiOrder->cart();

        // todo extend WC_ORDER -> set_affilicon_cart_id()
        $this->wcOrder->delete_meta_data('affilicon_cart_id');

        $this->wcOrder->add_meta_data('affilicon_cart_id', $cart->getCartId());

        /** @var WC_Order_Item_Product $wcLineItem */
        foreach ($this->wcOrder->get_items() as $wcLineItem) {

            $affiliconProductId = getMetaDataValue($wcLineItem->get_product(), 'affilicon_product_id');

            // todo handling exception
            $cart->addLineItem($affiliconProductId, $wcLineItem->get_quantity());

        }

        $this->wcOrder->save();
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getRegionCode($code)
    {
        $code = strtolower($code);

        $mapper = [
            'us' => 'en_US',
            'de' => 'de_DE',
            'it' => 'it_IT',
            'es' => 'es_ES',
            'fr' => 'fr_FR',
        ];

        return $mapper[$code];
    }
}