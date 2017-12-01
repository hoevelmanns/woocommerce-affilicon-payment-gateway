<?php

/**
 * Created by PhpStorm.
 * User: marcelle
 * Date: 01.12.17
 * Time: 09:41
 */
trait Address
{
    /**
     * Maps the address for the given type
     * @param string $type
     * @return array
     */
    public function mapper($type)
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
        $address =  $this->mapper('billing');

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
        return $this->mapper('shipping');
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
}