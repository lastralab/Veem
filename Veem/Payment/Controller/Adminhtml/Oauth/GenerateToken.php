<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 21/12/18
 * Time: 01:10 PM
 */

namespace Veem\Payment\Controller\Adminhtml\Oauth;


use Magento\Backend\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class GenerateToken extends Action
{
    const OAUTH_URL = ''; // TODO: tbd by Veem
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ZendClient
     */
    protected $client;

    protected $json;

    protected $configWriter;

    protected $logger;

    public function __construct(
        Action\Context $context,
        ConfigInterface $config,
        ZendClient $client,
        Json $json,
        Writer $configWriter,
        LoggerInterface $logger
    )
    {
        $this->config = $config;
        $this->client = $client;
        $this->json = $json;
        $this->configWriter = $configWriter;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $clientId = $this->config->getValue('merchant_id');
        $clientSecret = $this->config->getValue('merchant_secret');

        $key = base64_encode("{$clientId}:{$clientSecret}");

        /** @var JsonResult $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $website = $this->_request->getParam('website');

        $store = $this->_request->getParam('store');

        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeId = 0;

        if(!empty($website)) {
            $scope = ScopeInterface::SCOPE_WEBSITE;
            $scopeId = $website;
        }

        if(!empty($store)) {
            $scope = ScopeInterface::SCOPE_STORE;
            $scopeId = $store;
        }

        try {
            $this->client
                ->setUri(self::OAUTH_URL)
                ->setMethod(ZendClient::POST)
                ->setHeaders('Authentication Basic', $key)
                ->setParameterPost([]); // TODO: set Params if needed

            $response = $this->client->request();

            if($response->isError()) {
                $this->logger->error($response->getBody());
                $this->prepareResult($result, 500);
            }

            $body = $response->getBody();

            $responseBody = $this->json->unserialize($body);

            $this->configWriter->save('payment/veem/merchant_token', $responseBody['access_token'], $scope, $scopeId);
        } catch (\Zend_Http_Client_Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->prepareResult($result, 500);
        }

        $data = [
            'msg' => __('Token Generated Correctly')
        ];

        $this->prepareResult($result, 200, $data);

        return $result;
    }

    protected function prepareResult(JsonResult $result, int $code, array $data = [])
    {
        $result->setHttpResponseCode($code);

        $result->setData($data);
    }
}