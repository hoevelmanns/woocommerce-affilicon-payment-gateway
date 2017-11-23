<?php

require 'includes/affilicon-php-api-client/vendor/autoload.php';
require 'includes/class-wc-affilicon-payment-gateway-checkout.php';
require 'includes/class-wc-affilicon-payment-gateway-itns-service.php';

/**
 * Class WC_Affilicon_Payment_Gateway
 */

class WC_Affilicon_Payment_Gateway extends WC_Payment_Gateway
{
    // todo phpdoc
    /** @var WC_Order order */
    public $order;
    public $sandbox;
    public $testMode;
    public $receiver_email;
    public $vendor_id;
    public $itns_url;
    public $itns_secret_key;
    public $itns_prefix;

    /** @var  WC_Affilicon_Payment_Gateway_ItnsService */
    protected $itnsService;

    const CHECKOUT_FORM_VERSIONS = [
        '4' => 'Checkout form 4',
        '3' => 'Modern (flat design / Responsive)',
        '2' => 'Classic (conventional theme)' // todo i18n
    ];

    const AFFILICON_PRODUCT_TYPES = [
        '1' => 'Standard product', // todo i18n
        '2' => 'Subscription product'
    ];

    public function init()
    {
        // define additional product attributes for woocommerce product
        // todo get from config file
        if (!defined('extraProductFields')) {
            define('extraProductFields', [
                'affilicon_product_id' => [
                    'placeholder' => __('Please enter your affilicon product id', 'woocommerce-affilicon-payment-gateway'),
                    'label' => __('affilicon Product-ID', 'woocommerce-affilicon-payment-gateway'),
                    'type' => 'text',
                    'class' => 'short',
                    'wrapper_class' => 'form-field'
                ],
                'affilicon_product_type' => [
                    'placeholder' => __('Please select the type of your affilicon product', 'woocommerce-affilicon-payment-gateway'),
                    'label' => __('affilicon Product type', 'woocommerce-affilicon-payment-gateway'),
                    'type' => 'select',
                    'class' => 'select short',
                    'wrapper_class' => 'form-field',
                    'options' => self::AFFILICON_PRODUCT_TYPES
                ]
            ]);
        }

        $this->id = 'affilicon_payment';
        $this->method_title = __('Affilicon Payment', 'woocommerce-affilicon-payment-gateway');
        $this->title = __('Affilicon Payment', 'woocommerce-affilicon-payment-gateway');
        $this->has_fields = true;
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('affilicon_custom_method_name') ?: 'affilicon payment';
        $this->description = $this->get_option('description');
        $this->sandbox = $this->get_option('sandbox') !== 'no';
        $this->testMode = $this->get_option('test_mode') !== 'no';
        $this->receiver_email = $this->get_option('receiver_email');
        $this->vendor_id = $this->get_option('vendor_id');
        $this->itns_url = $this->get_option('affilicon_itns_url');
        $this->itns_secret_key = $this->get_option('affilicon_itns_secret');
        $this->itns_prefix = $this->get_option('affilicon_itns_prefix'); // optional
        $this->receiver_email = "marcelle.hoevelmanns@gmail.com"; // todo option

        $this->has_fields = true;

        if (is_admin()) {
            $this->init_form_fields();
            $this->init_settings();
        }

        /** @var \AffiliconApiClient\Client $affiliconClient */
        $affiliconClient = \AffiliconApiClient\Client::getInstance();

        $affiliconClient
            ->setEnv($this->sandbox ? 'staging' : 'production')
            ->setTestMode($this->testMode)
            ->setSecretKey($this->itns_secret_key)
            ->setCountryId('de')// todo get from woocommerce
            ->setUserLanguage('de_DE')// todo get from wordpress/woocommerce
            ->setClientId($this->vendor_id)
            ->init();

        $this->itnsService = new WC_Affilicon_Payment_Gateway_ItnsService($affiliconClient);
    }

    public function __construct()
    {
        $this->init();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // generate custom product fields
        add_action('woocommerce_product_options_general_product_data', array($this, 'custom_woocommerce_product_fields'));

        // save custom field inputs
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_woocommerce_product_fields'));

    }

    /**
     * @param int $orderId
     * @return array|WP_Error
     */
    public function process_payment($orderId)
    {
        $this->order = wc_get_order($orderId);
        /** @var WC_Affilicon_Payment_Gateway_Checkout $checkoutForm */
        $checkout = new WC_Affilicon_Payment_Gateway_Checkout($this->order);

        try {
            // until we support the legacy form, we need to check the version and call the legacy form preparer
            if (intval($this->get_option('affilicon_checkout_form_theme')) < 3) { // todo get selected checkout form
              // todo  $checkout->buildLegacyFormUrl();
            } else {
                // checkout form 3 with widget
                $checkout->createOrder();
            } // todo use case for checkout form 4

        } catch (Exception $e) {
            return [
                'result' => 'failed',
                'message' => $e->getMessage()
            ];
        }

        return array(
            'result' => 'success',
            'redirect' => $checkout->getUrl()//@todo testmodus berücksichtigen
        );

    }

    public function getTransactionUrl($order)
    {
        return parent::get_transaction_url($order);
    }

    public function payment_fields()
    {
        ?>
        <style>
            div.payment_method_affilicon_payment fieldset {
                display: block !important;
                border: 0 !important;
                margin: 0 !important;
            }
        </style>
        <fieldset>
            <p class="form-row form-row-wide">

                <img src="https://www.affilicon.net/wp-content/uploads/2015/05/affilicon_Logo_Google_143x59.png" alt="">
            <h3>Sichere und bequeme Bezahlung mit affilicon.</h3>
            <p><a href="https://www.affilicon.net" target="_blank">Weitere Informationen zur affilicon GmbH</a></p>
            </p>
            <div class="clear"></div>
        </fieldset>
        <?php
    }


    public function custom_woocommerce_product_fields()
    {
        foreach (extraProductFields as $key => $field) {
            $this->add_woocommerce_field(array_merge($field, [
                'id' => $key,
                'name' => $key,
            ]));
        }
    }

    public function add_woocommerce_field(Array $field)
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
     * iterate defined custom fields and store input values
     * @param $post_id
     */
    public function save_custom_woocommerce_product_fields($post_id)
    {
        foreach (extraProductFields as $key => $field) {
            $fieldValue = isset($_POST[$key]) ? $_POST[$key] : '';
            $product = wc_get_product($post_id);
            $product->update_meta_data($key, $fieldValue);
            $product->save();
        }
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable Affilicon Payment', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'yes'
            ),

            'sandbox' => array(
                'title' => __('Sandbox', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'no'
            ),

            'test_mode' => array(
                'title' => __('Test mode', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enabled', 'woocommerce-affilicon-payment-gateway'),
                'default' => 'no'
            ),

            // todo option order theme
            'affilicon_custom_method_name' => array(
                'title' => __('Custom Payment-Method Title', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'text',
                'description' => __('Custom name of payment-method', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('affilicon Payment', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
                'readonly' => true
            ),

            // todo option order theme
            'affilicon_checkout_form_theme' => array(
                'title' => __('Theme of Checkout form', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'select',
                'description' => __('This controls the checkout form theme', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('3', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
                'readonly' => true,
                'options' => self::CHECKOUT_FORM_VERSIONS
            ),

            // $orderFormTheme

            'affilicon_itns_secret' => array(
                'title' => __('Affilicon Secret-Key', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'text',
                'description' => __('', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
                'readonly' => true
            ),

            'vendor_id' => array(
                'title' => __('Vendor ID', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'text',
                'description' => __('Vendor ID', 'woocommerce-affilicon-payment-gateway'),
                'default' => __('', 'woocommerce-affilicon-payment-gateway'),
                'desc_tip' => true,
            ),


            'description' => array(
                'title' => __('Customer Message', 'woocommerce-affilicon-payment-gateway'),
                'type' => 'textarea',
                'css' => 'width:500px;',
                'default' => '', // todo default description
                'description' => __('The message which you want it to appear to the customer in the checkout page.', 'woocommerce-affilicon-payment-gateway'),
            )

        );
    }

    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_options()
    {
        ?>
        <h3><?php _e('Affilicon Payment Settings', 'woocommerce-affilicon-payment-gateway'); ?></h3>
        <img src="https://my.affilicon.net/_files/images/affilicon_Logo_Google_143x59.png" alt="">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <table class="form-table">
                        <?php $this->generate_settings_html(); ?>
                    </table><!--/.form-table-->
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">

                        <div class="postbox">
                            <div class="handlediv" title="Click to toggle"><br></div>
                            <h3 class="hndle"><span><i class="dashicons dashicons-admin-tools"></i>&nbsp;&nbsp;ITNS-Einstellungen</span>
                            </h3>
                            <div class="inside">
                                <h4>Woocommerce Anbindung einrichten</h4>
                                Affilicon MY-Bereich -> Produkte -> Produkt bearbeiten -> Anbindungen -> "Neue Anbindung
                                hinzufügen":<br><br>

                                <div class="support-widget" style="display: inline-block;">
                                    <p>
                                        <strong>ITNS-URL:</strong><br>
                                        <?php echo get_site_url() ?>/affilicon/payment
                                    </p>
                                    <p>
                                        <strong>Secret Key:</strong><br>
                                        <?php echo $this->get_option('affilicon_itns_secret') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="postbox">
                            <div class="handlediv" title="Click to toggle"><br></div>
                            <h3 class="hndle"><span><i class="dashicons dashicons-editor-help"></i>&nbsp;&nbsp;Plugin Support</span>
                            </h3>
                            <div class="inside">
                                <div class="support-widget">
                                    <a href="https://support.affilicon.net/index.php?/Knowledgebase/Article/View/103/0/itns-instant-transaction-notification-service---anbindung">ITNS-Dokumentation</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <style type="text/css">
            .wpruby_button {
                background-color: #4CAF50 !important;
                border-color: #4CAF50 !important;
                color: #ffffff !important;
                width: 100%;
                padding: 5px !important;
                text-align: center;
                height: 35px !important;
                font-size: 12pt !important;
            }
        </style>
        <?php
    }
}