<?php

/**
 * Class AbstractTransaction
 */
abstract class AbstractTransaction
{
    /** @var Document */
    protected $document;
    /** @var Payment */
    protected $payment;
    /** @var string */
    protected $productId;
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
    /** @var object */
    private $custom;
    /** @var string */
    private $cartId;
    /** @var WC_Order */
    protected $order;
    /** @var array */
    protected $orderLineItems;

    const PRODUCT_ID_META_NAME = 'affilicon_product_id';
    const STATE_PREFIX = 'affilicon';

    /**
     * AbstractTransaction constructor.
     * @param object $requestData
     */
    public function __construct($requestData)
    {
        $this->setCustom(json_decode($requestData->custom));
        $this->setCartId($requestData->cartId);
        $this->setProductId($requestData->productId);
        $this->setType($requestData->type);


        $this->payment = (new Payment())
            ->setState('sale')
            ->setMethod($requestData->paymentMethod);

        $this->document = (new Document())
            ->setFile($requestData->documentFile)
            ->setDocumentId($requestData->documentId);

        $orderId = $this->custom->data->wc_order_id;

        $this->order = new Order($orderId);
    }

    public function execute()
    {

    }

    public function payment()
    {
        return $this->payment;
    }

    public function document()
    {
        return $this->document;
    }

    public function order()
    {
        return $this->order;
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
     * @return object
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @param object $custom
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;
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

    /**
     * Sets the item state of the product from requested transaction
     *
     */
    public function updateLineItemStates()
    {
        $wcLineItems = $this->order()->get_items();

        /** @var WC_Order_Item_Product $item */
        foreach ($wcLineItems as $item) {

            /** @var WC_Product $product */
            $product = $item->get_product();

            $itemProductId = getMetaDataValue($product, self::PRODUCT_ID_META_NAME);

            if ($itemProductId === $this->getProductId()) {
                $this->applyState($product);
            }
        }
    }

    /**
     * @return bool
     */
    protected function lineItemsFulfilled()
    {
        $fulfilled = 0;

        foreach ($this->orderLineItems as $orderLineItem) {

            if ($this->lineItemFulfilled($orderLineItem)) {

                $fulfilled++;

            }

        }

        return $fulfilled === count($this->orderLineItems);
    }

    /**
     * @param WC_Order_Item_Product
     * @return bool
     */
    protected function lineItemFulfilled($lineItem)
    {
        return !empty(getMetaDataValue($lineItem, self::STATE_PREFIX . '_' . $this->getType()));
    }

    /**
     * @param WC_Product $item $item
     */
    protected function applyState($item)
    {
        $transactionType = $this->getType();

        $metaKey = self::STATE_PREFIX . '_' . $transactionType;

        if (empty(getMetaDataValue($item, $metaKey))) {

            $item->add_meta_data($metaKey, 1);

            $item->save();

        }
    }
}