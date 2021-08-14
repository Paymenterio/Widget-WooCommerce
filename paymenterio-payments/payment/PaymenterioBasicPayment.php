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
use Paymenterio\Payments\Services\PaymenterioException;

class PaymenterioBasicPayment extends PaymenterioPaymentAbstract
{
    public function __construct()
    {
        $this->id = 'paymenterio_basic_payment';
        $this->icon = $this->get_option('icon') === "yes" ? plugins_url('paymenterio-payments/img/payment/paymenterio.png') : null;
        $this->method_title = 'Paymenterio';
        $this->method_description = 'Metoda umożliwia dokonanie wpłaty przekierowując klienta na stronę wyboru dowolnej metody płatności.';
        $this->title = $this->get_option('title');

        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __('Włączony / Wyłączony', 'paymenterio_payment'),
                'label'     => __('Włącz metodę płatności', 'paymenterio_payment'),
                'type'      => 'checkbox',
                'default'   => 'yes',
            ),
            'title' => array(
                'title'     => __('Tytuł', 'paymenterio_payment'),
                'type'      => 'text',
                'default' => __('Płatność Paymenterio', 'paymenterio_payment'),
                'desc_tip'  => __('Tytuł płatności widoczny dla użytkownika w momencie wybierania metody płatności', 'paymenterio_payment'),
            ),
            'icon' => array(
                'title'     => __('Wyświetl / Ukryj', 'paymenterio_payment'),
                'label'     => __('Wyświetl ikonę płatności', 'paymenterio_payment'),
                'type'      => 'checkbox',
                'default'   => 'yes',
                'desc_tip'  => __('Aktywując tą opcję wyświetlisz ikonę związaną z tą formą płątności obok jej nazwy.', 'paymenterio_payment'),
            )
        );
    }

    public function payment_fields() {}

    public function process_payment($order_id)
    {
        try {
            $order = wc_get_order($order_id);
            $shop = $this->getPaymenterioShop();
            $urls = $this->getReturnUrlsForOrder($order);
            $paymentData = null;

            try {
                $paymentData = $shop->createPayment(
                    1,
                    $order_id,
                    $this->getAmountForOrder($order),
                    $this->getNameForOrder($order),
                    $urls['successUrl'],
                    $urls['failUrl'],
                    $urls['notifyUrl']
                );
            } catch (PaymenterioException $e) {
                exit ($e);
            }

            $order->add_order_note(__('Rozpoczęcie płatności Paymenterio', 'paymenterio_payment'));
            WC()->cart->empty_cart();

            return array(
                    'result'   => 'success',
                    'redirect' =>  $paymentData->payment_link,
            );
        } catch (Exception $e) {
            wc_add_notice(__('Wystąpił problem przy rozpoczęciu płatności. Spróbuj ponownie.', 'paymenterio_payment'), 'error');
            return;
        }
    }
    
    public function admin_options()
    {
        include_once(__DIR__.'/../view/admin/option/basic.php');
    }
}
