<?php

namespace Veem\Payment\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{

    private $additionalInformationList = [
        'email',
        'fname',
        'lname',
        'country',
        'phone'
    ];

    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        /** @var DataObject $data */
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $method->getInfoInstance();

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    "veem_{$additionalInformationKey}",
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}