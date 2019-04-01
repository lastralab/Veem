<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 17/12/18
 * Time: 01:25 PM
 */
namespace Veem\Payment\Model\Config\Source;

class PayBtn implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => 'Default',
                'value' => 'default'
            ],
            [
                'label' => 'Style 1 (Blue)',
                'value' => 'blue'
            ],
            [
                'label' => 'Style 2 (White)',
                'value' => 'wht'
            ],
            [
                'label' => 'Style 3 (Blue) - Large',
                'value' => 'lg--blue'
            ],
            [
                'label' => 'Style 4 (White) - Large',
                'value' => 'lg--wht'
            ]
        ];
    }
}