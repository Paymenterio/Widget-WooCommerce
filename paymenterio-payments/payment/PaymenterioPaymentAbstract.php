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
use \Paymenterio\Payments\Helpers\SignatureGenerator;

abstract class PaymenterioPaymentAbstract extends WC_Payment_Gateway
{
    private $shop;
    
    public function getPaymenterioShop()
    {
        if ($this->shop === null) {
            $this->shop = new Paymenterio\Payments\Shop(PaymenterioSettingsModel::getShopId(), PaymenterioSettingsModel::getApiKey());
        }

        return $this->shop;
    }

    public function getAmountForOrder($order)
    {
        return array(
            "value"=>$order->get_total(),
            "currencyCode"=>$order->get_currency()
        );
    }

    public function getNameForOrder($order) {
        return "Płatność za zamówienie {$order->get_id()}";
    }

    public function getReturnUrlsForOrder($order)
    {
        return array(
            'successUrl' =>  $this->get_return_url($order),
            'failUrl' => $order->get_cancel_order_url(),
            'notifyUrl' => $this->buildNotifyUrl($order)
        );
    }

    private function buildNotifyUrl($order) {
        $url = get_site_url() . PaymenterioSettingsModel::NOTIFY_URL_PATH;
        return str_replace('{{$hash}}', SignatureGenerator::generateSHA1Signature($order->get_id(), $order->get_order_key()), $url);
    }

}
