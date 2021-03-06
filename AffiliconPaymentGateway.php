<?php

require 'includes/affilicon-php-api-client/vendor/autoload.php';
require 'includes/helpers.php';
require 'includes/traits/Address.php';
require 'includes/models/Document.php';
require 'includes/models/Order.php';
require 'includes/models/Payment.php';
require 'includes/services/OrderService.php';
require 'includes/models/AbstractTransaction.php';
require 'includes/models/SaleTransaction.php';
require 'includes/models/RefundTransaction.php';
require 'includes/models/ChargebackTransaction.php';
require 'includes/services/ItnsService.php';

/**
 * Class AffiliconPaymentGateway
 *
 * @property string $id
 * @property string $method_title
 * @property string $title
 * @property boolean $has_fields
 * @property boolean $enabled
 * @property string $description
 * @property array $form_fields
 * @property WC_Order $order
 * @property boolean $sandbox
 * @property boolean $testPurchase
 * @property boolean $checkoutVersion
 * @property integer $formConfigId
 * @property string $receiver_email
 * @property string $vendor_id
 * @property string $itns_url
 * @property string $itns_secret_key
 * @property string $itns_prefix
 * @property array $extraProductFields
 */

class AffiliconPaymentGateway extends WC_Payment_Gateway
{
    /** @var ItnsService */
    protected $itnsService;
    /** @var OrderService */
    protected $checkout;

    public function __construct()
    {
        $this->init();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // generate custom product fields
        add_action('woocommerce_product_options_general_product_data', array($this, 'custom_woocommerce_product_fields'));

        // save custom field inputs
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_woocommerce_product_fields'));

        $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';

        load_plugin_textdomain( 'woocommerce-affilicon-payment-gateway', false, $plugin_rel_path );
    }

    /**
     * Initializes the plugin
     *
     * @return void
     */
    public function init()
    {
        $this->id = 'affilicon_payment';
        $this->method_title = __('AffiliCon Payment', 'woocommerce-affilicon-payment-gateway');
        $this->title = __('AffiliCon Payment', 'woocommerce-affilicon-payment-gateway');
        $this->has_fields = true;
        $this->enabled = $this->get_option('enabled');
        $this->title =  __('AffiliCon Payment', 'woocommerce-affilicon-payment-method-name');
        $this->sandbox = false; //$this->get_option('sandbox') !== 'no';
        $this->description = ""; //todo customer message necessary? $this->get_option('description');
        $this->testPurchase = $this->get_option('test_purchase') !== 'no';
        $this->checkoutVersion = $this->get_option('checkout_version');
        $this->receiver_email = $this->get_option('receiver_email');
        $this->vendor_id = $this->get_option('vendor_id');
        $this->formConfigId = $this->get_option('affilicon_form_configuration_id');
        $this->itns_url = $this->get_option('affilicon_itns_url');
        $this->itns_secret_key = $this->get_option('affilicon_itns_secret');
        $this->itns_prefix = $this->get_option('affilicon_itns_prefix'); // optional
        $this->has_fields = true;

        if (is_admin()) {

            // define additional product attributes for woocommerce product
            // todo get from config file
            $this->extraProductFields = [

                'affilicon_product_id' => [
                    'placeholder' => __('Please enter your affilicon product id', 'woocommerce-affilicon-payment-gateway'),
                    'label' => __('AffiliCon Product-ID', 'woocommerce-affilicon-payment-gateway'),
                    'type' => 'text',
                    'class' => 'short',
                    'wrapper_class' => 'form-field'
                ],
                'affilicon_product_is_subscription' => [
                    'placeholder' => __('Is this a subscription product?', 'woocommerce-affilicon-payment-gateway'),
                    'label' => __('AffiliCon Subscription product?', 'woocommerce-affilicon-payment-gateway'),
                    'type' => 'checkbox',
                ]
            ];

            $this->init_form_fields();

            $this->init_settings();

            return;
        }

        /** @var \AffiliconApiClient\Client $apiClient */
        $apiClient = \AffiliconApiClient\Client::getInstance();

        try {

            $apiClient
                ->setEnv($this->sandbox ? 'staging' : 'production')
                ->setTestPurchase($this->testPurchase)
                ->setSecretKey($this->itns_secret_key)
                ->setCountryId('de')// todo get from woocommerce
                ->setUserLanguage('de_DE')// todo get from wordpress/woocommerce
                ->setClientId($this->vendor_id);

            if (!$this->useLegacyCheckout()) {
                $apiClient->setFormConfigId($this->formConfigId);
            }

            $apiClient->init();

        } catch (Exception $e) {

            // todo add exception class

        }

        $this->itnsService = new ItnsService($apiClient);
    }

    /**
     * @param int $orderId
     * @return array
     * @throws \AffiliconApiClient\Exceptions\ConfigurationInvalid
     */
    public function process_payment($orderId)
    {
        $this->order = wc_get_order($orderId);

        $this->checkout = new OrderService($this->order);

        if ($this->useLegacyCheckout()) {

            return $this->process_payment_legacy();

        }

        return $this->process_payment_microservice();
    }

    public function useLegacyCheckout() {

        return $this->checkoutVersion === "3";

    }

    public function process_payment_microservice()
    {

        try {

            $this->checkout->createOrderWithCart();

        } catch (Exception $e) {

            return [
                'result' => 'failed',
                'message' => $e->getMessage()
            ];

        }

        return array(
            'result' => 'success',
            'redirect' => $this->checkout->getCheckoutUrl()
        );
    }

    public function process_payment_legacy()
    {
        try {

            $this->checkout->createOrder();

        } catch (Exception $e) {

            return [
                'result' => 'failed',
                'message' => $e->getMessage()
            ];

        }

        return array(
            'result' => 'success',
            'redirect' => $this->checkout->getCheckoutUrlLegacy()
        );

    }

    /**
     * @return void
     */
    public function custom_woocommerce_product_fields()
    {
        foreach ($this->extraProductFields as $key => $field) {
            $this->add_woocommerce_field(array_merge($field, [
                'id' => $key,
                'name' => $key,
            ]));
        }
    }

    /**
     * @param array $field
     */
    public function add_woocommerce_field($field)
    {
        switch ($field['type']) {
            case 'text': {
                woocommerce_wp_text_input($field);
                break;
            }
            case 'select': {
                woocommerce_wp_select($field);
                break;
            }
            case 'textarea': {
                woocommerce_wp_textarea_input($field);
                break;
            }
            case 'checkbox': {
                woocommerce_wp_checkbox($field);
                break;
            }
        }
    }

    /**
     * Iterates defined custom fields and stores the input values
     * @param $post_id
     */
    public function save_custom_woocommerce_product_fields($post_id)
    {
        $productKeys = array_keys($this->extraProductFields);

        foreach ($productKeys as $key) {

            $fieldValue = isset($_POST[$key]) ? $_POST[$key] : '';
            $product = wc_get_product($post_id);
            $product->update_meta_data($key, $fieldValue);
            $product->save();
            
        }
    }

    /**
     * Initializes the form input fields of the plugin settings
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = [

            'enabled' => [
                'title' => __('Use AffiliCon Payment', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'yes'
            ],
            /*'sandbox' => [
                'title' => __('Use Sandbox', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'no'
            ],*/
            'test_purchase' => [
                'title' => __('Show Test Purchase payment method in checkout form', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'no'
            ],
            'vendor_id' => [
                'title' => __('Vendor ID', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'text',
                'description' => __('Vendor ID', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
                'required' => 'required'
            ],
            'checkout_version' => [
                'title' => __('Checkout Version', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'select',
                'description' => __('Checkout Version', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
                'required' => 'required',
                'options' => [
                    '3' => __('Checkout form 3 (Single-Product-Version)'),
                    '3.1' => __('Checkout form 3 (Cart-Version)'),
                    //'4' => 'Checkout form 4'
                ]
            ],
            'affilicon_itns_secret' => [
                'title' => __('Secret-Key', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'text',
                'description' => __('', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
            ],
            'affilicon_form_configuration_id' => [
                'title' => __('Checkout Form Config ID', 'woocommerce-affilicon-payment-gateway_label-form-config-id'),
                'type' => 'text',
                'description' => __('', 'woocommerce-affilicon-payment-gateway_desc-form-config-id'),
                'desc_tip' => true,
            ]
        ];
    }

    /**
     * Gets the template for the admin settings options
     *
     * @return void
     */
    public function admin_options()
    {
       include_once 'templates/admin_options.php';
    }

    /**
     * Gets the template for the payment fields
     *
     * @return void
     */
    public function payment_fields()
    {
        include_once 'templates/payment_fields.php';
    }

    public function mm_jobs_plugins_loaded() {
        load_plugin_textdomain( 'mm-jobs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


}