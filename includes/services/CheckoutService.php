<?php

/**
 * Class CheckoutService
 */
class CheckoutService
{
    /** @var AffiliconPaymentGateway $gateway */
    public $gateway;

    /** @var string */
    private $checkoutUrl;

    /** @var WC_Order $order */
    private $order;

    /** @var \AffiliconApiClient\Models\Order */
    private $affiliconOrder;


    public function __construct(WC_Order $order)
    {
        $this->order = $order;
    }

    /**
     * Maps the address for the given type
     * @param $type
     * @return array
     */
    public function address($type)
    {
        $address = [
            'company' => call_user_func([$this->order, "get_{$type}_company"]),
            'firstname' => call_user_func([$this->order, "get_{$type}_first_name"]),
            'lastname' => call_user_func([$this->order, "get_{$type}_last_name"]),
            'address_1' => call_user_func([$this->order, "get_{$type}_address_1"]),
            'address_2' => call_user_func([$this->order, "get_{$type}_address_2"]),
            'city' => call_user_func([$this->order, "get_{$type}_city"]),
            'postcode' => call_user_func([$this->order, "get_{$type}_postcode"]),
            'country' => call_user_func([$this->order, "get_{$type}_country"])
        ];

        return $address;
    }

    /**
     * Returns the mapped WooCommerce basic address
     * @return array
     */
    public function wcBasicAddress()
    {
        /* at the moment the customer data does not differ from the billing data, therefore
           we use the billing address */

        return $this->wcBillingAddress();
    }

    /**
     * Returns the mapped WooCommerce billing address
     * @return array
     */
    public function wcBillingAddress()
    {
        $address =  $this->address('billing');

        $address['email'] = $this->order->get_billing_email();
        $address['phone'] = $this->order->get_billing_phone();

        return $address;
    }

    /**
     * Returns the mapped WooCommerce shipping address
     * @return array
     */
    public function wcShippingAddress()
    {
        return $this->address('shipping');
    }

    /**
     * Creates a new cart and passes the Woocommerce cart items.
     *
     * @return void
     */
    public function createOrder()
    {
        $this->affiliconOrder = new \AffiliconApiClient\Models\Order();

        $this->addItnsCallback();

        $this->addBillingData();

        $this->addShippingData();

        $this->addBasicAddressData();

        $this->buildCart();

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
        $this->affiliconOrder
            ->setCallbackUrl($this->getTransactionEndpoint())
            //->setCallbackUrl('https://hookb.in/ZV8nBgOk')
            ->setCallbackItnsTypeId('15'); // todo define // Woocommerce Payment Gateway

        $this->affiliconOrder->addCallbackData([
            'data' => [
                'wc_order_id' => $this->order->get_id(),
                'wc_order_key' => $this->order->get_order_key(),
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
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->affiliconOrder->getCheckoutUrl();
    }


    /**
     * Adds the woocommerce billing data to affilicon order
     */
    public function addBillingData()
    {
        $this->affiliconOrder->setBillingAddress($this->wcBillingAddress());
    }

    /**
     * Adds the woocommerce shipping data to affilicon order
     */
    public function addShippingData()
    {
        $this->affiliconOrder->setShippingAddress($this->wcShippingAddress());
    }

    /**
     * Adds the woocommerce basic address data to affilicon order
     */
    public function addBasicAddressData()
    {
        $this->affiliconOrder->setBasicAddress($this->wcBasicAddress());
    }

    /**
     * Build the affilicon cart
     */
    public function buildCart()
    {
        /** @var \AffiliconApiClient\Models\Cart $cart */
        $cart = $this->affiliconOrder->cart();

        // todo extend WC_ORDER -> set_affilicon_cart_id()

        if (getMetaDataValue($this->order, 'affilicon_cart_id')){
            $this->order->delete_meta_data('affilicon_cart_id');
        }

        $this->order->add_meta_data('affilicon_cart_id', $cart->getCartId());

        /** @var WC_Order_Item $wcLineItem */
        foreach ($this->order->get_items() as $wcLineItem) {

            $affiliconProductId = getMetaDataValue($wcLineItem->get_product(), 'affilicon_product_id');

            if ($affiliconProductId) {

                $cart->addLineItem($affiliconProductId, $wcLineItem->get_quantity());

            }

        }

        $this->order->save();
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