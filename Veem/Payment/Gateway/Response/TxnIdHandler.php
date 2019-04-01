<?php

namespace Veem\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;
use Veem\Payment\Gateway\Helper\Response;

class TxnIdHandler implements HandlerInterface
{

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment = $paymentDO->getPayment();
        if(isset($response['skip_validation'])) {
            return;
        }

        $payment->setAdditionalInformation('veem_invoice_id', $response[Response::VEEM_INVOICE_ID]);
        $payment->setAdditionalInformation('veem_email', $response[Response::VEEM_PAYER][Response::VEEM_EMAIL]);

        if(isset($response[Response::VEEM_PAYER][Response::VEEM_FNAME])) {
            $payment->setAdditionalInformation('veem_fname', $response[Response::VEEM_PAYER][Response::VEEM_FNAME]);
        }

        if(isset($response[Response::VEEM_PAYER][Response::VEEM_LNAME])) {
            $payment->setAdditionalInformation('veem_lname', $response[Response::VEEM_PAYER][Response::VEEM_LNAME]);
        }

        $payment->setAdditionalInformation('veem_payment_status', $response[Response::VEEM_STATUS]);

        $payment->setAdditionalInformation('veem_country', $response[Response::VEEM_PAYER][Response::VEEM_COUNTRY]);
        $payment->setAdditionalInformation('veem_phone', $response[Response::VEEM_PAYER][Response::VEEM_PHONE]);
        $payment->setAdditionalInformation('claim_link', $response[Response::VEEM_CLAIM_LINK]);

        $payment->setTransactionId($response[Response::VEEM_TXN_ID]);
        $payment->setIsTransactionClosed(false);

        $amount = $payment->formatAmount($payment->getBaseAmountOrdered(), true);
        $payment->setBaseAmountAuthorized($amount);

        $amount = $payment->formatAmount($payment->getAmountOrdered(), true);
        $payment->setAmountAuthorized($amount);

        $transaction = $payment->addTransaction(TransactionInterface::TYPE_AUTH);
        $message = __("Order has been placed, we will process it until payment is sent. Veem Invoice ID: %1", $response[Response::VEEM_INVOICE_ID]);
        $message = $payment->prependMessage($message);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        $message = __(
            "Veem amount invoiced: %1 %2",
            $payment->formatAmount($response[Response::VEEM_AMOUNT][Response::VEEM_AMOUNT_NUMBER]),
            $response[Response::VEEM_AMOUNT][Response::VEEM_AMOUNT_CURRENCY]
        );
        $message = $payment->prependMessage($message);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        $message = __("Veem invoice status: %1", $response[Response::VEEM_STATUS]);
        $message = $payment->prependMessage($message);
        $payment->addTransactionCommentsToOrder($transaction, $message);
    }
}