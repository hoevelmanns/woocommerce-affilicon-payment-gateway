<?php

/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Cart.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * Date: 04.10.17
 */

/**
 * Class WC_Affilicon_Payment_Gateway_Checkout
 */

class WC_Affilicon_Payment_Gateway_Checkout
{

    /** @var WC_Affilicon_Payment_Gateway $gateway */
    public $gateway;

    private $checkoutUrl;

    /** @var  WC_Order $order */
    private $order;

    /** @var \AffiliconApiClient\Models\Order */
    private $affiliconOrder;

    public function __construct(WC_Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param WC_Order_Item $item
     * @param string $key
     * @return bool
     */
    public function getMetaDataValue($item, $key)
    {
        foreach ($item->get_meta_data() as $meta) {
            if ($meta->key === $key) {
                return $meta->value;
            }
        }
        return false;
    }

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

    public function wcBasicAddress()
    {
        $address =  $this->address('billing');
        $address['email'] = $this->order->get_billing_email();
        return $address;
    }

    public function wcBillingAddress()
    {
        $address =  $this->address('billing');
        $address['email'] = $this->order->get_billing_email();
        return $this->address('billing');
    }

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

        $this->addHookRegisterData();

        $this->addBillingData();

        $this->addShippingData();

        $this->addBasicAddressData();

        $this->buildCart();

        $this->checkoutUrl = $this->getCheckoutUrl();

    }

    protected function addHookRegisterData()
    {
        $this->affiliconOrder->addCustomData([
            'register' => [
                'itns_type_id' => 15,
                'url' => "https://requestb.in/1j6gd9i1" //todo "core" should be checking the itns_name in ITNS.php newItns()
            ],
            'data' => [
                'wc_order_id' => $this->order->get_id(),
                'wc_order_key' => $this->order->get_order_key(),
            ]
        ]);
    }

    protected function getCheckoutUrl()
    {
        $this->generateCheckoutUrl();
        return $this->checkoutUrl;
    }

    protected function generateCheckoutUrl()
    {
        $this->checkoutUrl = $this->affiliconOrder->generateCheckoutUrl();
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
        $cart = $this->affiliconOrder->cart();

        // todo extend WC_ORDER -> set_affilicon_cart_id()

        $this->order->add_meta_data('affilicon_cart_id', $cart->getCartId());

        /** @var WC_Order_Item $wcLineItem */
        foreach ($this->order->get_items() as $wcLineItem) {

            $affiliconProductId = $this->getMetaDataValue($wcLineItem->get_product(), 'affilicon_product_id');

            if ($affiliconProductId) {

                $cart->addLineItem($affiliconProductId, $wcLineItem->get_quantity());

            }

        }

        $this->order->save();
    }

    /**
     * Get the checkout form url from the created order
     * @return mixed
     */
    public function getUrl()
    {
        return $this->affiliconOrder->getCheckoutUrl();
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