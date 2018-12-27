<?php
/**
 * Created by PhpStorm 2018.3.1
 * Author: Tania
 * Date: 13th December 2018
 */

namespace Veem\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Veem\Payment\Gateway\Http\Client\Client;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'veem_payment';
    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        Client::SUCCESS => __('Success'),
                        Client::FAILURE => __('Fraud')
                    ]
                ]
            ]
        ];
    }
}