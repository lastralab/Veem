<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/12/18
 * Time: 04:42 PM
 */

namespace Veem\Payment\Gateway\Request;


use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Helper\Formatter;

class PaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();

        $result = [
            'amount' => [
                'currency' => $order->getCurrencyCode(),
                'number' => $this->formatPrice(SubjectReader::readAmount($buildSubject))
            ],

            'payer' => [
                'businessName' => '',
                'countryCode' => $billingAddress->getCountryId(),
                'email' => $billingAddress->getEmail(),
                'firstName' => $billingAddress->getFirstname(),
                'lastName' => $billingAddress->getLastname(),
                'type' => 'Incomplete',
                'phone' => $billingAddress->getTelephone()
            ]
        ];

        return $result;
    }
}