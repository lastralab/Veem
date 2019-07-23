<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/12/18
 * Time: 05:13 PM
 */

namespace Veem\Payment\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;

class AccountType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => 'Incomplete',
                'value' => 'Incomplete'
            ],
            [
                'label' => 'Business',
                'value' => 'Business'
            ],
            [
                'label' => 'Personal',
                'value' => 'Personal'
            ]
        ];
    }
}