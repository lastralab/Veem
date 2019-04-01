<?php

namespace Veem\Payment\Block\Adminhtml\Order\View;

use Veem\Payment\Model\Ui\ConfigProvider;

class RequestMoney extends \Magento\Backend\Block\Template
{
    private $urlBuilder;
    private $_coreRegistry;
    private $currencyHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper,
        $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        $this->_coreRegistry = $registry;
        $this->currencyHelper = $currencyHelper;
        parent::__construct($context, $data);
    }

    public function getRequestMoneyUrl()
    {
        return $this->urlBuilder->getUrl('veem/requests/requestMoney');
    }

    public function getCurrentTotalAmount()
    {
        return $this->currencyHelper->currency($this->getOrder()->getPayment()->getAmountOrdered(),false,false);
    }

    public function canShowButton()
    {
        $payment = $this->getOrder()->getPayment()->getMethod();

        if($payment !== ConfigProvider::CODE) {
            return false;
        }

        return !$this->getOrder()->hasInvoices() && !$this->getPayment()->getAuthorizationTransaction();
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    public function getPayment()
    {
        return $this->getOrder()->getPayment();
    }

}