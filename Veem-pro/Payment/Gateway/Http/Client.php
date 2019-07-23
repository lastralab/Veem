<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 10/01/19
 * Time: 11:37 AM
 */

namespace Veem\Payment\Gateway\Http;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

class Client implements ClientInterface
{
    /**
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $converter;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ConfigInterface
     */
    private $config;

    private $checkCall;

    /**
     * @param ZendClientFactory $clientFactory
     * @param Logger $logger
     * @param ConfigInterface $config
     * @param ConverterInterface | null $converter
     * @param boolean $checkCall
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        Logger $logger,
        ConfigInterface $config,
        ConverterInterface $converter = null,
        $checkCall = false
    ) {
        $this->clientFactory = $clientFactory;
        $this->converter = $converter;
        $this->logger = $logger;
        $this->config = $config;
        $this->checkCall = $checkCall;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Zend_Http_Client_Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        if($this->checkCall && !$this->config->getValue('request_money')) {
            return [
                'skip_validation' => true
            ]; //Skipping Request Money per Merchant configs
        }

        $log = [
            'request' => $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];
        $result = [];
        /** @var ZendClient $client */
        $client = $this->clientFactory->create();

        $client->setConfig($transferObject->getClientConfig());
        $client->setMethod($transferObject->getMethod());

        switch ($transferObject->getMethod()) {
            case \Zend_Http_Client::GET:
                $client->setParameterGet($transferObject->getBody());
                break;
            case \Zend_Http_Client::POST:
                $client->setRawData($transferObject->getBody());
                break;
            default:
                throw new \LogicException(
                    sprintf(
                        'Unsupported HTTP method %s',
                        $transferObject->getMethod()
                    )
                );
        }

        $client->setHeaders($transferObject->getHeaders());
        $client->setUrlEncodeBody($transferObject->shouldEncode());
        $client->setUri($transferObject->getUri());

        try {
            $response = $client->request();

            $result = $this->converter
                ? $this->converter->convert($response->getBody())
                : [$response->getBody()];
            $log['response'] = $result;

            if(!$response->isSuccessful()) {
                throw new LocalizedException(__("%1", $result["message"]), null, $result["code"]);
            }

        } catch (\Zend_Http_Client_Exception $e) {
            throw new \Magento\Payment\Gateway\Http\ClientException(
                __($e->getMessage())
            );
        } catch (\Magento\Payment\Gateway\Http\ConverterException $e) {
            throw $e;
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}