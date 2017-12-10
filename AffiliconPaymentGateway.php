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
    /** @var  ItnsService */
    protected $itnsService;

    public function __construct()
    {
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // generate custom product fields
        add_action('woocommerce_product_options_general_product_data', array($this, 'custom_woocommerce_product_fields'));

        // save custom field inputs
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_woocommerce_product_fields'));

        $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
        load_plugin_textdomain( 'woocommerce-affilicon-payment-gateway', false, $plugin_rel_path );

        $this->init();
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
        $this->description = ""; //todo customer message necessary? $this->get_option('description');
        $this->sandbox = $this->get_option('sandbox') !== 'no';
        $this->testPurchase = $this->get_option('test_purchase') !== 'no';
        $this->receiver_email = $this->get_option('receiver_email');
        $this->vendor_id = $this->get_option('vendor_id');
        $this->formConfigId = $this->get_option('affilicon_form_configuration_id');
        $this->itns_url = $this->get_option('affilicon_itns_url');
        $this->itns_secret_key = $this->get_option('affilicon_itns_secret');
        $this->itns_prefix = $this->get_option('affilicon_itns_prefix'); // optional
        $this->receiver_email = "marcelle.hoevelmanns@gmail.com"; // todo option
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
                ]
            ];

            $this->init_form_fields();

            $this->init_settings();

            return;
        }

        /** @var \AffiliconApiClient\Client $affiliconClient */
        $affiliconClient = \AffiliconApiClient\Client::getInstance();

        $affiliconClient
            ->setEnv($this->sandbox ? 'staging' : 'production')
            ->setTestPurchase($this->testPurchase)
            ->setSecretKey($this->itns_secret_key)
            ->setCountryId('de')// todo get from woocommerce
            ->setUserLanguage('de_DE')// todo get from wordpress/woocommerce
            ->setFormConfigId($this->formConfigId)
            ->setClientId($this->vendor_id)
            ->init();

        $this->itnsService = new ItnsService($affiliconClient);
    }

    /**
     * @param int $orderId
     * @return array|WP_Error
     */
    public function process_payment($orderId)
    {
        $this->order = wc_get_order($orderId);

        /** @var OrderService $checkoutForm */
        $checkout = new OrderService($this->order);

        try {

            $checkout->createOrder();

        } catch (Exception $e) {

            return [
                'result' => 'failed',
                'message' => $e->getMessage()
            ];
            
        }

        return array(
            'result' => 'success',
            'redirect' => $checkout->getCheckoutUrl()//@todo testmodus berücksichtigen
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
            'sandbox' => [
                'title' => __('Use Sandbox', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'no'
            ],
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

}