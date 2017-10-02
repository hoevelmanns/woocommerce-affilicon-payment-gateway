<?php

/**
 * Class WC_Affilicon_Payment_Gateway
 */
class WC_Affilicon_Payment_Gateway extends WC_Payment_Gateway
{

  CONST CHECKOUT_FORM_VERSIONS = [
    '4' => 'Checkout form 4',
    '3' => 'Modern (flat design / Responsive)',
    '2' => 'Classic (conventional theme'
  ];

  public function __construct()
  {

    // Includes
    if (!is_admin()) {
      include_once('includes/class-wc-affilicon-payment-gateway-request.php');
      include_once('includes/class-wc-affilicon-payment-gateway-response.php');
      include_once('includes/class-wc-affilicon-payment-gateway-itns-handler.php');
      include_once('includes/api-client/Api.php');
      include_once('includes/api-client/Cart.php');
    }


    $this->id = 'affilicon_payment';
    $this->method_title = __('Affilicon Payment', 'woocommerce-affilicon-payment-gateway');
    $this->title = __('Affilicon Payment', 'woocommerce-affilicon-payment-gateway');
    $this->has_fields = true;
    $this->init_form_fields();
    $this->init_settings();
    $this->enabled = $this->get_option('enabled');
    $this->title = $this->get_option('affilicon_custom_method_name') ?: 'affilicon payment';
    $this->description = $this->get_option('description');

    //@todo following fields
    $this->testmode = $this->get_option('testmode') !== "no";

    $this->receiver_email = $this->get_option('receiver_email');
    $this->vendor_id = $this->get_option('vendor_id');

    $this->itns_url = $this->get_option('affilicon_itns_url');
    $this->itns_secret_key = $this->get_option('affilicon_itns_secret');
    $this->itns_prefix = $this->get_option('affilicon_itns_prefix'); // optional

    $this->receiver_email = "marcelle.hoevelmanns@gmail.com"; // todo option

    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

    // Versuch
    add_filter('query_vars', array($this, 'add_query_vars'));
    add_action('parse_request', array($this, 'check_for_itns_request'));
    add_action('init', array($this, 'addEndpoint'));
  }


  public function addEndpoint()
  {
    add_rewrite_rule('^affilicon/payment/?([0-9]+)?/?', 'index.php?affilicon=1&payment=$matches[1]&', 'top');
  }

  public function add_query_vars($vars)
  {
    $vars[] = 'affilicon';
    $vars[] = 'payment';
    $vars[] = 'data';

    return $vars;
  }

  public function check_for_itns_request($query)
  {
    // todo später woanders hin?
    global $wp;
    // todo: schöner -> add_rewrite_rule
    if ($wp->request !== 'affilicon/payment') {
      return;
    }

    $itnsHandler = new WC_Affilicon_Payment_Gateway_ITNS_Handler($this);
    $itnsHandler->checkResponse($query);
  }


  public function process_payment($orderId)
  {
    //header('Content-type: application/json'); // todo kann weg?

    // handler checkout form 3 / multiple products, simple product or subscription
    $this->order = wc_get_order($orderId);
    $checkoutForm = new WC_Affilicon_Payment_Gateway_Request($this, $this->order);

    try {
      // until we support the legacy form, we need to check the version and call the legacy form preparer
      if (intval($this->get_option('affilicon_checkout_form_theme')) < 4) { // todo get selected checkout form
        $checkoutForm->prepareLegacyForm();
      } else {
        // checkout form 4
        $checkoutForm->prepareCheckoutForm();
      }
    } catch (Exception $e) {
      return new WP_Error('affilicon_payment_error_prepare_checkout_form', $e->getMessage(), array( 'status' => $e->getCode() ));
    }


    return array(
      'result' => 'success',
      'redirect' => $checkoutForm->getCheckoutFormUrl()//@todo testmodus berücksichtigen
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
              border:0 !important;
              margin: 0!important;
          }
          #payment_method_affilicon_payment {
              display: inline-block !important;
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

  public function init_form_fields()
  {

    $this->form_fields = array(
      'enabled' => array(
        'title' => __('Enable/Disable', 'woocommerce-affilicon-payment-gateway'),
        'type' => 'checkbox',
        'label' => __('Enable affilicon Payment', 'woocommerce-affilicon-payment-gateway'),
        'default' => 'yes'
      ),
      'testmode' => array(
        'title' => __('Enable/Disable Testmode', 'woocommerce-affilicon-payment-gateway'),
        'type' => 'checkbox',
        'label' => __('Enable Testmode', 'woocommerce-affilicon-payment-gateway'),
        'default' => 'no'
      ),

      // todo option order theme
      'affilicon_custom_method_name' => array(
        'title' 		=> __( 'Custom Payment-Method Title', 'woocommerce-affilicon-payment-gateway' ),
        'type' 			=> 'text',
        'description' 	=> __( 'Custom name of payment-method', 'woocommerce-affilicon-payment-gateway' ),
        'default'		=> __( 'affilicon Payment', 'woocommerce-affilicon-payment-gateway' ),
        'desc_tip'		=> true,
        'readonly'		=> true
      ),

      // todo option order theme
      'affilicon_checkout_form_theme' => array(
        'title' 		=> __( 'Theme of Checkout form', 'woocommerce-affilicon-payment-gateway' ),
        'type' 			=> 'select',
        'description' 	=> __( 'This controls the checkout form theme', 'woocommerce-affilicon-payment-gateway' ),
        'default'		=> __( '3', 'woocommerce-affilicon-payment-gateway' ),
        'desc_tip'		=> true,
        'readonly'		=> true,
        'options'       => self::CHECKOUT_FORM_VERSIONS
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
                              Affilicon MY-Bereich -> Produkte -> Produkt bearbeiten -> Anbindungen -> "Neue Anbindung hinzufügen":<br><br>

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