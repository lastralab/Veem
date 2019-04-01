<?php

namespace Veem\Payment\Controller\Adminhtml\Requests;


use Braintree\Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Veem\Payment\Gateway\Http\TransferFactory;
use Veem\Payment\Gateway\Request\PaymentDataBuilder;
use Magento\Framework\Registry;
use Veem\Payment\Gateway\Http\Client;
use Magento\Sales\Api\OrderRepositoryInterface;
use Veem\Payment\Gateway\Response\TxnIdHandler;

class RequestMoney extends Action
{

    const ADMIN_RESOURCE = 'Veem_Payment::request_money';

    /**
     * @var OrderInterface
     */
    protected $order;
    /**
     * @var TransferFactory
     */
    protected $transferFactory;

    /**
     * @var PaymentDataBuilder
     */
    protected $paymentDataBuilder;

    /**
     * @var PaymentDataObjectFactory
     */
    protected $paymentDataFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var TxnIdHandler
     */
    protected $handler;

    public function __construct(
        Action\Context $context,
        TransferFactory $transferFactory,
        OrderRepositoryInterface $orderRepository,
        PaymentDataBuilder $paymentDataBuilder,
        Registry $registry,
        PaymentDataObjectFactory $paymentDataFactory,
        Client $client,
        TxnIdHandler $handler
    )
    {
        $this->transferFactory = $transferFactory;
        $this->paymentDataBuilder = $paymentDataBuilder;
        $this->paymentDataFactory = $paymentDataFactory;
        $this->orderRepository = $orderRepository;
        $this->_coreRegistry = $registry;
        $this->client = $client;
        $this->handler = $handler;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Zend_Http_Client_Exception
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $amount = $this->_request->getParam('amount');
        $notes = $this->_request->getParam('notes');
        $orderId = $this->_request->getParam('order_id');

        try {
            $payment = $this->getPayment($orderId);
            $payment->setAmountOrdered($amount);
            $payment->setNotes($notes);

            $paymentDataObject = $this->paymentDataFactory->create($payment);
            $paymentDataInfo = $this->paymentDataBuilder->build(['payment' => $paymentDataObject]);


            $transfer = $this->transferFactory->create($paymentDataInfo);

            $response = $this->client->placeRequest($transfer);

            $this->handler->handle(['payment' => $paymentDataObject], $response);

            if(isset($response['message'])) {
                $data['msg'] = $response['message'];
                $data['success'] = false;
            } else {
                $data['msg'] = __('Request Money Complete');
                $data['success'] = true;
            }
            $code = 200;

            $this->messageManager->addSuccessMessage($data['msg']);

            $this->orderRepository->save($this->order);
        } catch(\Exception $e) {
            $data['msg'] = $e->getMessage();
            $code = 500;
            $this->messageManager->addErrorMessage($data['msg']);
        }

        $this->prepareResult($result, $code, $data);

        return $result;
    }

    protected function prepareResult(JsonResult $result, int $code, array $data = [])
    {
        $result->setHttpResponseCode($code);

        $result->setData($data);
    }

    protected function getOrder($orderId)
    {
        $this->order = $this->orderRepository->get($orderId);

        return $this->order;
    }

    protected function getPayment($orderId)
    {
        $this->getOrder($orderId);
        return $this->order->getPayment();
    }
}