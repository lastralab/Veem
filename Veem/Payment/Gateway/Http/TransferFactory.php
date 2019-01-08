<?php

namespace Veem\Payment\Gateway\Http;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\Math\Random;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Random
     */
    private $random;

    private $uri;

    /**
     * @param TransferBuilder $transferBuilder
     * @param ConfigInterface $config
     * @param Random $random
     * @param string $uri
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        ConfigInterface $config,
        Random $random,
        $uri = ''
    ) {
        $this->transferBuilder = $transferBuilder;
        $this->config = $config;
        $this->random = $random;
        $this->uri = $uri;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     * @throws LocalizedException
     */
    public function create(array $request)
    {
        return $this->transferBuilder
            ->setBody($request)
            ->setMethod(ZendClient::POST)
            ->setHeaders(
                [
                    'X-Request-Id' => $this->random->getRandomString(30),
                    'Authorization' => sprintf('Bearer %s', $this->config->getValue('token'))
                ]
            )
            ->setUri($this->uri)
            ->shouldEncode(true)
            ->build();
    }
}