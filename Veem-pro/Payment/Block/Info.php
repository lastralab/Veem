<?php
/**
 * Created by PhpStorm 2018.3.1
 * Author: Tania
 * Date: 13th December 2018
 */

namespace Veem\Payment\Block;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Info extends ConfigurableInfo
{
    protected $_template = 'Veem_Payment::info/default.phtml';
    /**
     * @var OrderRepositoryInterface
     */

    private $orderRepository;
    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationAcquirer;

    /**
     * @var OrderInterface
     */
    private $order;

    public function __construct(
        Context $context,
        ConfigInterface $config,
        OrderRepositoryInterface $orderRepository,
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        array $data = []
    )
    {
        parent::__construct($context, $config, $data);
        $this->orderRepository = $orderRepository;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     * @return string | Phrase
     */
    protected function getValueView($field, $value)
    {
        switch ($field) {
            case PaymentInterface::KEY_ADDITIONAL_DATA:
                return implode(' ', $value);
        }
        return parent::getValueView($field, $value);
    }

    protected function initOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if($this->order === null && $orderId) {
            try {
                $this->order = $this->orderRepository->get($orderId);
            } catch (NoSuchEntityException $e) {
                $this->order = null;
            }
        }

        return $this->order;
    }

    public function getCountryName($value)
    {
        try {
            $country = $this->countryInformationAcquirer->getCountryInfo($value);
            $countryName = $country->getFullNameLocale();
        } catch (NoSuchEntityException $e) {
            $countryName = '';
        }

        return $countryName;
    }

    public function getOrderPayment()
    {
        $this->initOrder();

        $payment = $this->order ?
            $this->order->getPayment()->getAdditionalInformation() :
            false;

        return $payment;
    }

    public function getMethodTitle() {
        $this->initOrder();

        $title = $this->order ?
            $this->order->getPayment()->getMethod() :
            false;

        return $title;
    }

    public function getLinkButton($value)
    {
        $button = '<button class="claim-btn"><a class="claim-link" href="' . $value . '" target="_blank"></a>'. __('Send your payment').'</button>';
        return $button;
    }
}