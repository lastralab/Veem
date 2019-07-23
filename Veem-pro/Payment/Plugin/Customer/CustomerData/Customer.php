<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 29/01/19
 * Time: 10:21 AM
 */

namespace Veem\Payment\Plugin\Customer\CustomerData;


use Magento\Customer\Helper\Session\CurrentCustomer;

class Customer
{
    protected $currentCustomer;

    public function __construct(CurrentCustomer $currentCustomer)
    {
        $this->currentCustomer = $currentCustomer;
    }

    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        if($this->currentCustomer->getCustomerId()) {
            $customer = $this->currentCustomer->getCustomer();
            $result['email'] = $customer->getEmail();
        }

        return $result;
    }
}