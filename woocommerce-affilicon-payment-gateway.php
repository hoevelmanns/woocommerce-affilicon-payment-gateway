<?php
//todo modify plugin informations -> Plugin URI
/* @wordpress-plugin
 * Plugin Name:       WooCommerce Affilicon Payment Gateway
 * Plugin URI:        affilicon.net
 * Description:       WooCommerce Affilicon Payment Gateway
 * Version:           1.0.6
 * Author:            Marcelle Hövelmanns, AffiliCon GmbH
 * Author URI:        http://www.affilicon.net
 * Text Domain:       woocommerce-affilicon-payment-gateway
 * Domain Path: /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


// todo error reporting entfernen!
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('AFFILICON_CHECKOUT_FORM_URL_LEGACY', 'https://secure.affilibank.app');
define('AFFILICON_CHECKOUT_FORM_URL', 'https://secure.affilicon.net');
define('AFFILICON_SERVICE_URL', 'https://secure.affilicon.app');


$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(in_array('woocommerce/woocommerce.php', $active_plugins)){

  add_filter('woocommerce_payment_gateways', 'add_affilicon_payment_gateway');
  function add_affilicon_payment_gateway( $gateways ){
    $gateways[] = 'WC_Affilicon_Payment_Gateway';
    return $gateways;
  }

  add_action('plugins_loaded', 'init_affilicon_payment_gateway');
  function init_affilicon_payment_gateway(){
    if(!class_exists('WC_Payment_Gateway')) return;
    require 'class-woocommerce-affilicon-payment-gateway.php';
    $GLOBALS['affilicon_payment'] = new WC_Affilicon_Payment_Gateway();
  }

  add_action( 'plugins_loaded', 'affilicon_payment_load_plugin_textdomain' );
  function affilicon_payment_load_plugin_textdomain() {
    load_plugin_textdomain( 'woocommerce-affilicon-payment-gateway', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
  }

}
