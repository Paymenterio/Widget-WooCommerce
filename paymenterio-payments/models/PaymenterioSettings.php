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
class PaymenterioSettingsModel
{
    const NOTIFY_URL_PATH = '/wp-json/wc/v3/paymenterio-payments/notify?hash={{$hash}}';

    public static function getShopId()
    {
        return get_option('paymenterio_shop_id', '');
    }

    public static function getApiKey()
    {
        return get_option('paymenterio_api_key', '');
    }

    public static function getIsEnabled()
    {
        return get_option('paymenterio_is_enabled', false) == true;
    }


    public static function setShopId($paymenterio_shop_id)
    {
        update_option('paymenterio_shop_id', $paymenterio_shop_id);
    }

    public static function setApiKey($paymenterio_api_key)
    {
        update_option('paymenterio_api_key', $paymenterio_api_key);
    }

    public static function setIsEnabled($paymenterio_is_enabled = false)
    {
        update_option('paymenterio_is_enabled', $paymenterio_is_enabled == true);
    }

    public function save()
    {
        if (null !== wp_unslash($_POST['paymenterio_settings_request'])) {
            $paymenterio_shop_id = sanitize_text_field($_POST['paymenterio_shop_id']);
            $paymenterio_api_key = sanitize_text_field($_POST['paymenterio_api_key']);
            $paymenterio_is_enabled = isset($_POST['paymenterio_is_enabled']) && $_POST['paymenterio_is_enabled'] == true;

            update_option('paymenterio_shop_id', $paymenterio_shop_id);
            update_option('paymenterio_api_key', $paymenterio_api_key);
            update_option('paymenterio_is_enabled', $paymenterio_is_enabled);
        }
    }
}
