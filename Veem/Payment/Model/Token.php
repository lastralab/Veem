<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 04:19 PM
 */

namespace Veem\Payment\Model;


use Magento\Payment\Gateway\ConfigInterface;
use Veem\Payment\Model\Token\Config\Access;
use Veem\Payment\Model\Token\Generator;
use Veem\Payment\Model\Token\Pinger;

class Token
{
    /**
     * @var ConfigInterface
     */
    private $paymentConfig;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Pinger
     */
    private $pinger;

    /**
     * @var Access
     */
    private $access;

    public function __construct(
        ConfigInterface $paymentConfig,
        Generator $generator,
        Pinger $pinger,
        Access $access
    )
    {
        $this->paymentConfig = $paymentConfig;
        $this->generator = $generator;
        $this->pinger = $pinger;
        $this->access = $access;
    }

    /**
     * Method to get access token.
     * Please always use this method as it will validate token hasn't expired
     * @return string|null
     */
    public function get()
    {
        if(
            !$this->paymentConfig->getValue('merchant_id') ||
            !$this->paymentConfig->getValue('merchant_secret')
        ) {
            return null;
        }

        if($this->access->getToken() && $this->pinger->hello()) {
            return $this->access->getToken();
        }

        $this->generator->generate();

        return $this->access->getToken();
    }
}