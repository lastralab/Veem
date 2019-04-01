<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 04:42 PM
 */

namespace Veem\Payment\Model\Token;


use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\ZendClient;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Psr\Log\LoggerInterface;
use Veem\Payment\Api\Data\UrlBuilderInterface;
use Veem\Payment\Gateway\Http\Converter\JsonToArray;
use Veem\Payment\Model\Token\Config\Access;

class Generator
{
    const URI = '/oauth/token';

    const AUTH_CODE = 'client_credentials';

    /**
     * @var ConfigInterface
     */
    private $paymentConfig;

    /**
     * @var ZendClient
     */
    private $client;

    /**
     * @var UrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var JsonToArray
     */
    private $converter;

    /**
     * @var Writer
     */
    private $configWriter;

    /**
     * @var Manager
     */
    private $cacheManager;

    /**
     * @var Access
     */
    private $access;

    public function __construct(
        ConfigInterface $paymentConfig,
        ZendClient $client,
        UrlBuilderInterface $urlBuilder,
        LoggerInterface $logger,
        EncryptorInterface $encryptor,
        JsonToArray $converter,
        Writer $configWriter,
        Manager $cacheManager,
        Access $access
    )
    {
        $this->paymentConfig = $paymentConfig;
        $this->client = $client;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
        $this->converter = $converter;
        $this->configWriter = $configWriter;
        $this->cacheManager = $cacheManager;
        $this->access = $access;
    }


    public function generate()
    {
        $this->_clientRequest();
    }

    /**
     * @throws \Zend_Http_Client_Exception
     */
    private function _prepareClient()
    {
        $post = [
            'grant_type' => self::AUTH_CODE
        ];

        $uri = $this->urlBuilder->setUri(self::URI)->build();

        $clientId = $this->encryptor->decrypt(
            $this->paymentConfig->getValue('merchant_id')
        );
        $clientSecret = $this->encryptor->decrypt(
            $this->paymentConfig->getValue('merchant_secret')
        );
        $key = base64_encode("{$clientId}:{$clientSecret}");

        $this->client
            ->setUri($uri)
            ->setMethod(ZendClient::POST)
            ->setHeaders('Authorization', "Basic $key")
            ->setHeaders(ZendClient::CONTENT_TYPE, ZendClient::ENC_URLENCODED)
            ->setParameterPost($post);
    }

    private function _clientRequest()
    {
        try {
            $this->_prepareClient();
            $response = $this->client->request();
            $this->_processResponse($response);
        } catch (\Zend_Http_Client_Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        } catch (ConverterException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @param \Zend_Http_Response $response
     * @throws ConverterException
     */
    private function _processResponse(\Zend_Http_Response $response)
    {
        if($response->isError()) {
            $this->logger->error($response->getBody());
        }

        $body = $response->getBody();

        $result = $this->converter->convert($body);

        $token = $this->encryptor->encrypt($result['access_token']);

        $this->configWriter->save('payment/veem/access_token', $token);

        $this->access->setToken($token);

        // Cleaning Cache to get access to new saved token in future request
        $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
    }
}