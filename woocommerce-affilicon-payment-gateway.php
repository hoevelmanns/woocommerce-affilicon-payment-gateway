<?php
/* @wordpress-plugin
 * Plugin Name:       WooCommerce Affilicon Payment Gateway
 * Plugin URI:        affilicon.net
 * Description:       WooCommerce Affilicon Payment Gateway
 * Version:           1.2.3
 * Author:            Marcelle Hövelmanns, AffiliCon GmbH
 * Author URI:        http://www.affilicon.net
 * Text Domain:       woocommerce-affilicon-payment-gateway
 * Domain Path: /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

define('AFFILICON_REST_BASE_URI', 'affilicon/v1');
define('AFFILICON_REST_TRANSACTION_ROUTE', 'itns');

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(in_array('woocommerce/woocommerce.php', $active_plugins)){

  add_filter('woocommerce_payment_gateways', 'add_affilicon_payment_gateway');
  function add_affilicon_payment_gateway( $gateways ){

    $gateways[] = 'AffiliconPaymentGateway';
    return $gateways;

  }

  add_action('plugins_loaded', 'init_affilicon_payment_gateway');

  function init_affilicon_payment_gateway(){

    if (class_exists('WC_Payment_Gateway')) {
        require 'AffiliconPaymentGateway.php';
        $GLOBALS['affilicon_payment'] = new AffiliconPaymentGateway();
    };

  }
}
