<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 9/01/19
 * Time: 03:10 PM
 */

namespace Veem\Payment\Model\Token\Config;


use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Payment\Gateway\ConfigInterface;

class Access
{
    /**
     * @var ConfigInterface
     */
    private $paymentConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var null|string
     */
    private $token;

    public function __construct(
        ConfigInterface $paymentConfig,
        EncryptorInterface $encryptor
    )
    {
        $this->paymentConfig = $paymentConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if($this->token === null) {
            $this->token = $this->encryptor->decrypt($this->paymentConfig->getValue('access_token'));
        }

        return $this->token;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}