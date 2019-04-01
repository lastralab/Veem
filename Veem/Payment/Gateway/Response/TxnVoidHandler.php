<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 15/01/19
 * Time: 01:50 PM
 */

namespace Veem\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Veem\Payment\Gateway\Helper\Response;

class TxnVoidHandler implements HandlerInterface
{

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    private $paymentRepository;

    public function __construct(
        ConfigInterface $config,
        OrderPaymentRepositoryInterface $paymentRepository)
    {
        $this->config = $config;
        $this->paymentRepository = $paymentRepository;
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

        if(isset($response[Response::VEEM_STATUS])) {

            $payment->setAdditionalInformation('veem_payment_status', $response[Response::VEEM_STATUS]);

            $payment->setIsTransactionClosed(true);

            $this->paymentRepository->save($payment);
        } else {
            throw new LocalizedException(
                __('Error cancelling order - Unexpected Veem response: %s', print_r($response, true))
            );
        }
    }
}