<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        ItnsTransaction.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        23.11.17
 */

/**
 * Class Transaction
 */
class Transaction
{
    /** @var string */
    private $documentId;
    /** @var stdClass */
    private $documentFile;
    /** @var string */
    private $productId;
    /** @var string */
    private $amount;
    /** @var string */
    private $currency;
    /** @var string */
    private $type;
    /** @var string */
    private $state;
    /** @var string */
    private $ip;
    /** @var string */
    private $date;
    /** @var string */
    private $paymentMethod;
    /** @var object */
    private $custom;
    /** @var string */
    private $cartId;
    /** @var integer */
    private $wcOrderId;
    /** @var string */
    private $wcOrderKey;

    /**
     * @param $data
     * @return $this
     */
    public function set($data)
    {
        foreach ($data as $key => $value)
        {
            if (property_exists($this, $key)) {

                if ($key==='custom') {
                    $this->custom = json_decode($value);

                    if ($this->custom->data) {
                        $this->setWcOrderId($this->custom->data->wc_order_id);
                        $this->setWcOrderKey($this->custom->data->wc_order_key);
                    }

                    continue;
                }

                $this->{$key} = $value;

            }
        }

        return $this;
    }

    /** @param string $orderId */
    public function setWcOrderId($orderId)
    {
        $this->wcOrderId = $orderId;
    }

    /**
     * @return int
     */
    public function getWcOrderId()
    {
        return $this->wcOrderId;
    }

    /**
     * @param string $orderKey
     */
    public function setWcOrderKey($orderKey)
    {
        $this->wcOrderKey = $orderKey;
    }

    /**
     * @return string
     */
    public function getWcOrderKey()
    {
        return $this->wcOrderKey;
    }

    /**
     * @return string
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param string $documentId
     */
    public function setDocumentId(string $documentId)
    {
        $this->documentId = $documentId;
    }

    /**
     * @return stdClass
     */
    public function getDocumentFile()
    {
        return $this->documentFile;
    }

    /**
     * @param stdClass $documentFile
     */
    public function setDocumentFile($documentFile)
    {
        $this->$documentFile = $documentFile;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return object
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * @param object $customData
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @param string $cartId
     */
    public function setCartId($cartId)
    {
        $this->cartId = $cartId;
    }

}