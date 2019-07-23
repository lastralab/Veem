<?php

namespace Veem\Payment\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\HTTP\ZendClient;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Veem\Payment\Gateway\Helper\Response;

class VerifyPayment
{
    const VERIFY_URI = '/veem/v1.1/invoices/';

    /** @var LoggerInterface */
    protected $logger;

    /** @var \Veem\Payment\Api\InvoiceRepositoryInterface */
    protected $veemInvoiceRepository;

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var OrderPaymentRepositoryInterface */
    protected $paymentRepository;

    /** @var InvoiceService */
    protected $invoiceService;

    /** @var InvoiceRepositoryInterface */
    protected $invoiceRepository;

    /** @var TransactionFactory */
    protected $transactionFactory;

    /** @var TransactionRepositoryInterface */
    protected $transactionRepository;

    /** @var Order\Payment\Transaction\Builder */
    protected $transactionBuilder;

    public function __construct(
        LoggerInterface $logger,
        \Veem\Payment\Api\InvoiceRepositoryInterface $veemInvoiceRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderPaymentRepositoryInterface $paymentRepository,
        InvoiceService $invoiceService,
        InvoiceRepositoryInterface $invoiceRepository,
        TransactionFactory $transactionFactory,
        TransactionRepositoryInterface $transactionRepository,
        Order\Payment\Transaction\Builder $transactionBuilder
    )
    {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->veemInvoiceRepository = $veemInvoiceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->paymentRepository = $paymentRepository;
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->transactionBuilder = $transactionBuilder;
    }

    /**
     * Verify payment status of pending orders
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $newOrders = $this->getNewOrders();

        foreach ($newOrders as $order) {
            /** @var $payment Order\Payment */
            $payment = $order->getPayment();

            if(strpos(strtolower($payment->getAdditionalInformation('method_title')), 'veem') !== false) {
                $paymentId = $payment->getAdditionalInformation('veem_invoice_id');

                if(!$paymentId) {
                    continue;
                }

                try {
                    $invoiceDetails = $this->veemInvoiceRepository->get($paymentId);

                    if($invoiceDetails->getStatus() === null) {
                        $this->logger->error('Error while verifying payment status for invoice.', [(array)$invoiceDetails]);
                        throw new LocalizedException(__('Error while verifying payment status for invoice %s:', $paymentId));
                    }

                    $paymentStatus = $invoiceDetails->getStatus();

                    if($paymentStatus !== $payment->getAdditionalInformation('veem_payment_status')) {
                        $this->logger->info('Updating status for order #' . $order->getIncrementId());
                        $this->updateOrderState(
                            $paymentStatus,
                            $payment,
                            $order,
                            $invoiceDetails->getRequestId()
                        );
                    }

                } catch (LocalizedException $exception) {
                    $this->logger->warning('Skipping verification of payment due to exception.', [
                        'entity_id' => $order->getPayment()->getEntityId(),
                        'invoice_id' => $paymentId
                    ]);

                    $this->logger->error($exception->getMessage());
                } catch(\Exception $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @return OrderInterface[]
     */
    protected function getNewOrders()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('state', Order::STATE_NEW)->create();

        return $this->orderRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param string $type
     * @param $payment
     * @param $order
     * @param $requestId
     * @param bool $message
     * @return TransactionInterface|null
     */
    protected function createTransaction(string $type, $payment, $order, $requestId, $message = false) {
        $transaction = $this->transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setFailSafe(false)
            ->setTransactionId($requestId)
            ->build($type);

        if($message !== false) {
            $payment->addTransactionCommentsToOrder($transaction, $message);
        }

        return $transaction;
    }

    /**
     * @param string $paymentStatus
     * @param Order\Payment $payment
     * @param OrderInterface $order
     * @param string $requestId
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function updateOrderState($paymentStatus, Order\Payment $payment, OrderInterface $order, string $requestId)
    {
        $payment->setAdditionalInformation('veem_payment_status', $paymentStatus);
        $message = __("Veem invoice status: %1", $paymentStatus);
        $authTransaction = $payment->getAuthorizationTransaction();
        $payment->addTransactionCommentsToOrder($authTransaction, $message);
        $payment = $this->paymentRepository->save($payment);

        if($paymentStatus === Response::VEEM_STATUS_MARK_AS_PAID) {
            $this->generateOrderInvoice($order);

            $this->createTransaction(
                TransactionInterface::TYPE_CAPTURE,
                $payment,
                $order,
                $requestId,
                __("Payment has been completed. Order is processing.")
            );

            $order->setState(Order::STATE_PROCESSING);
        } else if($paymentStatus === Response::VEEM_STATUS_CANCELLED || $paymentStatus === Response::VEEM_STATUS_CLOSED) {
            $this->createTransaction(
                TransactionInterface::TYPE_VOID,
                $payment,
                $order,
                $requestId,
                __("Payment has been marked as {$paymentStatus} by Veem.")
            );

            /** @var $order Order */
            $order->cancel();
        }

        $order->setPayment($payment);

        $this->orderRepository->save($order);
    }

    /**
     * @param OrderInterface $order
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function generateOrderInvoice(OrderInterface $order)
    {
        /** @var $order \Magento\Sales\Model\Order  */
        if($order->canInvoice()) {
            $this->logger->info('Generating invoice for order #' . $order->getIncrementId());
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();

            $transaction = $this->transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transaction->save();
        } else {
            $this->logger->warning('Unable to generate requested invoice for order', [$order]);
        }
    }

}