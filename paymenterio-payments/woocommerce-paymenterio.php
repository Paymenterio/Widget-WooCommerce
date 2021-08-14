<?php
/*
Plugin Name: Paymenterio - Płatności WooCommerce
Plugin URI: https://paymenterio.pl
Description: Wtyczka integrująca bramkę płatniczą Paymenterio. Dzięki tej wtyczce możesz szybko i bezproblemowo przyjmować płatności za zakupy.
Version: 1.0.1
Author: Paymenterio
Author URI: https://paymenterio.pl
*/

if (! defined('ABSPATH')) {
    exit;
}

require_once 'helpers/Helpers.php';
require_once 'models/PaymenterioSettings.php';
require_once 'controller/PaymenterioSettings.php';
require_once 'callback/PaymenterioPaymentNotification.php';
require_once 'lib/sdk/Shop.php';

use \Paymenterio\Payments\Helpers\SignatureGenerator;

function paymenterio_payment_init()
{
    if (! class_exists('WC_Payment_Gateway')) {
        return;
    }
    if (PaymenterioSettingsModel::getIsEnabled()) {
        add_filter('woocommerce_payment_gateways', 'paymenterio_payment_load_class');
    }

    require_once 'payment/PaymenterioPaymentAbstract.php';
    require_once 'payment/PaymenterioBasicPayment.php';

    function paymenterio_payment_load_class($methods)
    {
        $methods[] = 'PaymenterioBasicPayment';
        return $methods;
    }
}

function paymenterio_settings_init()
{
    $settings = new PaymenterioSettingsController();
    $settings->init();
}

function paymenterio_payment_links($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=paymenterio-payments-settings') . '">' . __('Ustawienia', 'paymenterio-payments') . '</a>',
    );
    return array_merge($plugin_links, $links);
}

function admin_style()
{
    wp_enqueue_style('paymenterio_admin_menu_styles', plugins_url('paymenterio-payments/css/admin.css'));
}

add_action('admin_enqueue_scripts', 'admin_style');
add_action('plugins_loaded', 'paymenterio_payment_init');
add_action('plugins_loaded', 'paymenterio_settings_init');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'paymenterio_payment_links');
add_action( 'rest_api_init', function () {
    register_rest_route( 'wc/v3', 'paymenterio-payments/notify', array(
        'methods' => 'POST',
        'callback' => array(new PaymenterioPaymentNotification(), 'callback'),
    ));
});
