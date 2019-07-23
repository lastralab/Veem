<?php

namespace Veem\Payment\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClient;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Veem\Payment\Api\InvoiceRepositoryInterface;

class VoidRequest implements BuilderInterface
{
    const CANCEL_URI = '/veem/v1.1/invoices/{id}/cancel';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @param ConfigInterface $config
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(
        ConfigInterface $config,
        InvoiceRepositoryInterface $invoiceRepository
    )
    {
        $this->config = $config;
        $this->invoiceRepository = $invoiceRepository;
    }


    /**
     * @param array $buildSubject
     * @return array|bool
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDO->getPayment();

        $invoiceNumber = $payment->getAdditionalInformation('veem_invoice_id');

        if ($invoiceNumber !== null) {
            return [
                'uri' => str_replace('{id}', $invoiceNumber, self::CANCEL_URI),
                'method' => ZendClient::POST,
                'no_body' => true
            ];
        } else {
            throw new LocalizedException(__('Unable to void order with invalid invoice number %s', $invoiceNumber));
        }
    }
}