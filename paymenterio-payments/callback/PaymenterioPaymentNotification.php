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
use \Paymenterio\Payments\Helpers\SignatureGenerator;

class PaymenterioPaymentNotification
{
    private $shop = null;
    public function callback($request)
    {
        $hash = $request->get_params()['hash'];
        $body = json_decode(file_get_contents("php://input"), true);
        $statusID = 0;
        $orderID = 0;
        $order = null;

        if (isset($body['order']) && !empty($body['order'])) {
            $orderID = $body['order'];
            $order = wc_get_order($orderID);
        }

        if (isset($body['status']) && !empty($body['status'])) {
            $statusID = $body['status'];
        }

        if (empty($order)) {
            return new WP_Error( 'OrderNotFoundException', 'Order not found.', array( 'status' => 404 ) );
        }

        $orderKey = $order->get_order_key();
        $orderInternalStatus  = $order->get_status();
        $isSignatureValid = SignatureGenerator::verifySHA1Signature($orderID, $orderKey, $hash);
        if (!$isSignatureValid) {
            return new WP_Error( 'WrongSignatureException', 'Signature mismatch.', array( 'status' => 400 ) );
        }

        if ($orderInternalStatus != 'completed' && $orderInternalStatus != 'processing') {
            if ($statusID == 5) {
                $this->success($order, $statusID);
            } elseif ($statusID <= 4) {
                $this -> addNote($order, $statusID);
            } else {
                $this->error($order, $statusID);
            }
            return array(
                'code' => 'NotifiedSuccessfully',
                'message' => 'Order status has been changed.'
            );
        }
        return new WP_Error( 'PaymentNotFoundException', 'The payment was not found or was completed successfully.', array( 'status' => 404 ) );
    }

    public function success($order, $paymentStatus)
    {
        $verboseStatus = __('SUKCES', 'paymenterio_payment');
        $order->add_order_note(__('Płatność została przyjęta. Status płatności (' . $paymentStatus . ' - ' . $verboseStatus . ')', 'paymenterio_payment'));
        $order->payment_complete();
    }

    public function addNote($order, $paymentStatus) {
        $order->add_order_note(__('Status płatności uległ zmianie. Obecny status to ' . $paymentStatus . '.', 'paymenterio_payment'));
    }

    public function error($order, $paymentStatus)
    {
        $order->add_order_note(__('Płatność nie została przyjęta, system zwrócił status '.$paymentStatus, 'payments_payment'));
        $order->update_status('cancelled');
        $order->save();
    }
}
