<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 14/01/19
 * Time: 03:02 PM
 */

namespace Veem\Payment\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClient;
use Veem\Payment\Api\Data\InvoiceInterface;
use Veem\Payment\Api\InvoiceRepositoryInterface;
use Veem\Payment\Gateway\Http\Client;
use Veem\Payment\Gateway\Http\TransferFactory;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    const API_URI = '/veem/v1.1/invoices/';

    protected $transferFactory;

    protected $client;

    protected $invoiceFactory;

    public function __construct(
        TransferFactory $transferFactory,
        Client $client,
        InvoiceFactory $invoiceFactory
    )
    {
        $this->transferFactory = $transferFactory;
        $this->client = $client;
        $this->invoiceFactory = $invoiceFactory;
    }

    /**
     * Gets an invoice by id
     * @param int $id
     * @return InvoiceInterface
     * @throws LocalizedException
     */
    public function get($id)
    {
        try {
            $invoice = $this->requestInvoice($id);
            return $this->invoiceFactory->create()->setData($invoice);
        } catch(\Exception $e) {
            throw new LocalizedException(__('Unable to retrieve Veem invoice with if %s', $id), $e);
        }
    }

    /**
     * Cancels an invoice by id
     * @param int $id
     * @return void
     */
    public function cancel($id)
    {
        // TODO: Implement cancel() method.
    }

    /**
     * @param $invoiceId
     * @return mixed
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Zend_Http_Client_Exception
     */
    protected function requestInvoice($invoiceId) {
        $uri = self::API_URI . $invoiceId;

        $transfer = $this->transferFactory->setUri($uri)->setMethod(ZendClient::GET)->create();

        $response = $this->client->placeRequest($transfer);

        return $response;
    }
}