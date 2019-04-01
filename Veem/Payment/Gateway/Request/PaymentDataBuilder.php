<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/12/18
 * Time: 04:42 PM
 */

namespace Veem\Payment\Gateway\Request;


use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Helper\Formatter;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class PaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * @var ConfigInterface
     */
    private $config;

    private $dateTimeFactory;

    public function __construct(ConfigInterface $config, DateTimeFactory $dateTimeFactory)
    {
        $this->config = $config;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $this->validatePhone($payment);

        $dateTime = $this->dateTimeFactory->create();

        $result = [
            'amount' => [
                'currency' => $order->getCurrencyCode(),
                'number' => $this->formatPrice($payment->getAmountOrdered())
            ],

            'dueDate' => $dateTime->date('Y-m-d'),
            'externalInvoiceRefId' => $order->getOrderIncrementId(),

            'payer' => [
                'countryCode' => $payment->getAdditionalInformation('veem_country'),
                'email' => $payment->getAdditionalInformation('veem_email'),
                'firstName' => $payment->getAdditionalInformation('veem_fname'),
                'lastName' => $payment->getAdditionalInformation('veem_lname'),
                'type' => 'Incomplete',
                'phone' => $payment->getAdditionalInformation('veem_phone')
            ],

            'purposeOfPayment' => 'Goods'
        ];

        if(!empty($payment->getNotes())) {
            $result['notes'] = $payment->getNotes();
        }

        return $result;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @throws LocalizedException
     */
    private function validatePhone(\Magento\Payment\Model\InfoInterface $payment)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse(
                $payment->getAdditionalInformation('veem_phone'),
                $payment->getAdditionalInformation('veem_country')
            );

            $isValid = $phoneUtil->isValidNumber($numberProto);
            if($isValid) {
                $payment->setAdditionalInformation(
                    'veem_phone',
                    $phoneUtil->format(
                        $numberProto,
                        \libphonenumber\PhoneNumberFormat::INTERNATIONAL
                    )
                );
            } else {
                throw new LocalizedException(
                    __(
                        "Number is not valid for your country: %1",
                        $payment->getAdditionalInformation('veem_phone')
                    )
                );
            }
        } catch (\libphonenumber\NumberParseException $e) {
            throw new LocalizedException(
                __(
                    "Number format is incorrect for your country: %1",
                    $payment->getAdditionalInformation('veem_phone')
                )
            );
        }
    }
}