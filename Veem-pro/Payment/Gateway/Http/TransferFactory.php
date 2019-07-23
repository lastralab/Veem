<?php

namespace Veem\Payment\Gateway\Http;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\Math\Random;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Veem\Payment\Api\Data\UrlBuilderInterface;
use Veem\Payment\Gateway\Request\UUID;
use Veem\Payment\Model\Token;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var Token
     */
    private $token;

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

    /**
     * @var UrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $method;

    /**
     * @param Token $token
     * @param TransferBuilder $transferBuilder
     * @param ConfigInterface $config
     * @param Random $random
     * @param UrlBuilderInterface $urlBuilder
     * @param Json $serializer
     * @param string $uri
     * @param string $method
     */
    public function __construct(
        Token $token,
        TransferBuilder $transferBuilder,
        ConfigInterface $config,
        Random $random,
        UrlBuilderInterface $urlBuilder,
        Json $serializer,
        $uri = '',
        $method = ZendClient::POST
    ) {
        $this->token = $token;
        $this->transferBuilder = $transferBuilder;
        $this->config = $config;
        $this->random = $random;
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
        $this->uri = $uri;
        $this->method = $method;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request = [])
    {
        if(isset($request['uri'])) {
            $this->setUri($request['uri']);
        }

        if(isset($request['method'])) {
            $this->setMethod($request['method']);
        }

        if($this->method == ZendClient::POST) {
            if(isset($request['no_body'])) {
                $this->transferBuilder->setBody('');
            } else {
                $this->transferBuilder->setBody($this->serializer->serialize($request));
            }
        }

        $uri = $this->urlBuilder
            ->setUri($this->uri)
            ->build();

        $transferObject = $this->transferBuilder
            ->setMethod($this->method)
            ->setHeaders(
                [
                    'X-Request-Id' => $this->_generateRequestId(),
                    'Authorization' => sprintf('Bearer %s', $this->token->get()),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            )
            ->setUri($uri)
            ->shouldEncode(true)
            ->build();

        return $transferObject;
    }

    /**
     * @param $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method = ZendClient::POST)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    private function _generateRequestId()
    {
        return UUID::v4();
    }
}