<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 15/01/19
 * Time: 01:04 PM
 */

namespace Veem\Payment\Gateway\Validator;


use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class AvailabilityValidator extends AbstractValidator
{

    public function __construct(
        ResultInterfaceFactory $resultFactory
    )
    {
        parent::__construct($resultFactory);
    }

    public function validate(array $validationSubject)
    {
        $isValid = true;
        $paymentDO  = SubjectReader::readPayment($validationSubject);

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $paymentDO->getPayment()->getQuote();

        // Veem doesn't allow request money transactions below 25.00
        if($quote->getGrandTotal() < 25) {
            $isValid = false;
        }

        return $this->createResult($isValid);
    }
}