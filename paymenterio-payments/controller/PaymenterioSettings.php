<?php
/**
 *
 * Paymenterio Payment PHP SDK
 *
 * @author Paymenterio Development Team
 * @version 1.0.0
 * @license MIT
 * @copyright Paymenterio Sp. z o.o.
 *
 * https://paymenterio.pl
 *
 */
use \Paymenterio\Payments\Shop;

class PaymenterioSettingsController
{
    private $isSaved = false;
    private $isValid = false;
    private $isEnabled = false;
    
    public function init()
    {
        add_action('admin_menu', array( $this, 'add_options_page' ));
        add_action('admin_post', array( $this, 'save' ));
        add_action('admin_notices', array($this,'add_notice'));
    }
    
    public function add_options_page()
    {
        add_menu_page(
            'Paymenterio',
            'Paymenterio',
            'manage_options',
            'paymenterio-payments-settings',
            array( $this, 'render' ),
            'dashicons-admin-tools',
            56
        );
    }

    public function render()
    {
        $paymenterio_shop_id = PaymenterioSettingsModel::getShopId();
        $paymenterio_api_key = PaymenterioSettingsModel::getApiKey();
        $paymenterio_is_enabled = PaymenterioSettingsModel::getIsEnabled();

        include_once(__DIR__.'/../view/admin/settings.php');
    }

    public function save()
    {
        if (! ($this->has_valid_nonce() && current_user_can('manage_options'))) {
        } elseif (isset($_POST['paymenterio_settings_request'])) {
            $paymenterio_shop_id = sanitize_text_field($_POST['paymenterio_shop_id']);
            $paymenterio_api_key = sanitize_text_field($_POST['paymenterio_api_key']);
            $paymenterio_is_enabled = isset($_POST['paymenterio_is_enabled']) && $_POST['paymenterio_is_enabled'] == true;

            PaymenterioSettingsModel::setShopId($paymenterio_shop_id);
            PaymenterioSettingsModel::setApiKey($paymenterio_api_key);
            PaymenterioSettingsModel::setIsEnabled($paymenterio_is_enabled);

            $this->isSaved = true;

            $paymentData = null;

            try {
                $shop = new Shop($paymenterio_shop_id, $paymenterio_api_key);
                $paymentData = $shop->createPayment(1, 123456, array( "value"=>10.41, "currencyCode"=>"PLN" ), "Testowe zamówienie", 'SUCCESS_URL', 'FAIL_URL', 'NOTIFY_URL', true);
            } catch (Exception $e) {
                exit($e);
            }
            if ($paymentData !== null && isset($paymentData->status) && isset($paymentData->order)) {
                $this->isValid = true;
            }
        }
        
        $this->redirect();
    }

    private function has_valid_nonce()
    {
        if (! isset($_POST['paymenterio_settings_request'])) {
            return false;
        }
     
        $field  = wp_unslash($_POST['paymenterio_settings_request']);
        $action = 'paymenterio_settings_save';
     
        return wp_verify_nonce($field, $action);
    }

    private function redirect()
    {
        if (! isset($_POST['_wp_http_referer'])) {
            $_POST['_wp_http_referer'] = wp_login_url();
        }
     
        $url = urldecode(sanitize_text_field(
            wp_unslash($_POST['_wp_http_referer'])
        ));

        $redirectUrl = Helpers::addVarToUrl($url, "paymenterio_settings_save", $this->isSaved);
        $redirectUrl = Helpers::addVarToUrl($redirectUrl, "paymenterio_settings_validation", $this->isValid);

        wp_safe_redirect($redirectUrl);
        exit;
    }

    public function add_notice()
    {
        if (!isset($_GET['paymenterio_settings_save'])) {
            return;
        }

        if ($_GET['paymenterio_settings_save'] == true) {
            include_once(__DIR__.'/../view/admin/notice/success_save.php');
        } else {
            include_once(__DIR__.'/../view/admin/notice/error_save.php');
        }

        if ($_GET['paymenterio_settings_validation'] == true) {
            include_once(__DIR__.'/../view/admin/notice/success_validation.php');
        } else {
            include_once(__DIR__.'/../view/admin/notice/error_validation.php');
        }
    }
}
