<?php
/**
 * Created by PhpStorm 2018.3.1
 * Author: Tania
 * Date: 13th December 2018
 */

namespace Veem\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Gateway\Config\Config;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'veem';

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

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
                    'active' => $this->config->getValue('active'),
                    'button' => $this->config->getValue('pay_btn')
                ]
            ]
        ];
    }
}
