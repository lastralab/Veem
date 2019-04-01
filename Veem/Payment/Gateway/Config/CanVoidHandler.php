<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/12/18
 * Time: 03:16 PM
 */

namespace Veem\Payment\Gateway\Config;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Veem\Payment\Api\InvoiceRepositoryInterface;
use Veem\Payment\Gateway\Helper\Response;

class CanVoidHandler implements ValueHandlerInterface
{
    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    const FULFILLED_STATUSES = [
        Response::VEEM_STATUS_MARK_AS_PAID,
        Response::VEEM_STATUS_CLAIMED,
        Response::VEEM_STATUS_CANCELLED,
        Response::VEEM_STATUS_CLOSED
    ];

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {

        $this->invoiceRepository = $invoiceRepository;
    }

    public function handle(array $subject, $storeId = null)
    {
        $paymentDO = SubjectReader::readPayment($subject);

        $payment = $paymentDO->getPayment();

        $invoiceNumber = $payment->getAdditionalInformation('veem_invoice_id');

        if($invoiceNumber !== null) {
            $invoice = $this->invoiceRepository->get($invoiceNumber);

            if ($invoice->getStatus() !== null && in_array($invoice->getStatus(), self::FULFILLED_STATUSES)) {
                return false;
            }
        }

        return true;
    }
}