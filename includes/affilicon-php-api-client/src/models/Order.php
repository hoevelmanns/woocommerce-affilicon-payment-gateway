<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Order.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        11.11.17
 */

namespace AffiliconApiClient\Models;


use AffiliconApiClient\Abstracts\AbstractModel;
use AffiliconApiClient\Exceptions\ConfigurationInvalid;

/**
 * Class Order
 * @package AffiliconApiClient\Models
 */
class Order extends AbstractModel
{
    /** @var Cart */
    protected $cart;

    /** @var BillingAddress */
    private $billingAddress;

    /** @var ShippingAddress */
    private $shippingAddress;

    /** @var BasicAddress */
    private $basicAddress;

    /** @var array */
    private $customData = [];

    /** @var array */
    private $prefillData = [];

    /** @var  string */
    protected $checkoutUrl;

    /** @var  string */
    protected $callbackUrl;

    /** @var  string */
    protected $callbackItnsTypeId;

    public function cart()
    {
        if (!$this->cart instanceof Cart) {
            $this->cart = (new Cart())->create();
        }

        return $this->cart;
    }
    /**
     * Sets the shipping address of the order
     *
     * @param array $data
     */
    public function setShippingAddress($data)
    {
        if (!$this->shippingAddress instanceof ShippingAddress) {
            $this->shippingAddress = new ShippingAddress();
        }

        $this->shippingAddress->setData($data);
    }

    /**
     * Sets the billing address of the order
     *
     * @param array $data
     */
    public function setBillingAddress($data)
    {
        if (!$this->billingAddress instanceof BillingAddress) {
            $this->billingAddress = new BillingAddress();
        }

        $this->billingAddress->setData($data);
    }

    /**
     * Sets the basic address of the order
     *
     * @param array $data
     */
    public function setBasicAddress($data)
    {
        if (!$this->basicAddress instanceof BasicAddress) {
            $this->basicAddress = new BasicAddress();
        }

        $this->basicAddress->setData($data);
    }

    /**
     * Gets the shipping address of the order
     *
     * @return ShippingAddress
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Gets the billing address of the order
     *
     * @return BillingAddress
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Gets the basic address of the order
     *
     * @return BasicAddress
     */
    public function getBasicAddress()
    {
        return $this->basicAddress;
    }

    /**
     * Adds custom data to the order
     *
     * @param $data
     * @return array
     */
    public function addCustomData($data)
    {
        $this->customData = array_merge($this->customData, $data);

        return $this->customData;
    }

    /**
     * Sets the ITNS callback
     *
     * @param array $data
     * @return array|null
     */
    public function addCallbackData($data)
    {
        if (empty($this->callbackUrl) || empty($data)) {
            return null;
        }

        $data['register'] = [
            'itns_type_id' => $this->getCallbackItnsTypeId(),
            'url' => $this->getCallbackUrl()
        ];

        return $this->addCustomData($data);
    }

    /**
     * Sets the ITNS Type ID
     *
     * @param string $typeId
     * @return $this
     */
    public function setCallbackItnsTypeId($typeId)
    {
        $this->callbackItnsTypeId = $typeId;
        return $this;
    }

    /**
     * Gets the ITNS Type ID
     *
     * @return mixed
     */
    public function getCallbackItnsTypeId()
    {
        return $this->callbackItnsTypeId;
    }

    /**
     * Sets the ITNS callback url
     *
     * @param $callbackUrl
     * @return $this
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }


    /**
     * Gets the ITNS callback url
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Gets the custom data of the order
     *
     * @return array
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * Gets the complete checkout url with prefill data
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        if (empty($this->checkoutUrl)) {
            $this->generateCheckoutUrl();
        }

        return $this->checkoutUrl;
    }

    /**
     * Collects the prefill data
     *
     * @return array
     */
    protected function collectPrefillData()
    {
        $customData = [
            'custom' => json_encode($this->getCustomData())
        ];

        $prefillData = array_merge(
            $customData,
            $this->billingAddress->transform(),
            $this->shippingAddress->transform(),
            $this->basicAddress->transform()
        );

        if ($this->client->isTestPurchaseEnabled()) {
            $prefillData['testmode'] = 'true';
        }

        if ($this->client->hasFormConfiguration()) {
            $prefillData['formConfig'] = $this->client->getFormConfigId();
        }

        $this->prefillData = $prefillData;

        return $prefillData;
    }

    /**
     * Generates the checkout url with all parameters
     *
     * @throws ConfigurationInvalid
     */
    public function generateCheckoutUrl()
    {
        $env = $this->client->getEnv();

        if (!$env->secure_url) {
            throw new ConfigurationInvalid('Secure-URL is not defined. Check the configurations.');
        }

        $baseUrl = $env->secure_url;

        $this->checkoutUrl =  $this->addUrlParams($baseUrl);
    }

    /**
     * Adds necessary parameters to the given url
     *
     * @param string $baseUrl
     * @return string
     */
    protected function addUrlParams($baseUrl)
    {
        $prefillData = json_encode($this->collectPrefillData());
        $encryptedPrefillData = urlencode($this->client->encrypt($prefillData));

        $cartId = $this->cart()->getCartId();
        $clientId = $this->client->getClientId();
        $countryId = $this->client->getCountryId();
        $userLanguage = $this->client->getUserLanguage();
        $token = $this->client->getToken();

        $params = [
            "$clientId/redirect",
            "cartId/$cartId",
            "countryId/$countryId",
            "token/$token",
            "language/$userLanguage"
        ];

        return $baseUrl . "/" . join('/', $params) . "?prefill=$encryptedPrefillData";
    }

}