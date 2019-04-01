<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 9/01/19
 * Time: 03:04 PM
 */

namespace Veem\Payment\Model\Token;


use Magento\Framework\HTTP\ZendClient;
use Magento\Payment\Gateway\ConfigInterface;
use Veem\Payment\Api\Data\UrlBuilderInterface;
use Veem\Payment\Model\Token\Config\Access;

class Pinger
{
    const URI = '/veem/v1.1/hello';
    /**
     * @var ConfigInterface
     */
    private $paymentConfig;

    /**
     * @var UrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var ZendClient
     */
    private $client;

    /**
     * @var Access
     */
    private $access;

    public function __construct(
        ConfigInterface $paymentConfig,
        UrlBuilderInterface $urlBuilder,
        ZendClient $client,
        Access $access
    )
    {
        $this->paymentConfig = $paymentConfig;
        $this->urlBuilder = $urlBuilder;
        $this->client = $client;
        $this->access = $access;
    }

    public function hello()
    {
        $token = $this->access->getToken();
        $this->client
            ->setMethod(ZendClient::GET)
            ->setUri(
                $this->urlBuilder
                    ->setUri(self::URI)
                    ->build()
            )->setHeaders('Authorization', "Bearer $token");

        $response = $this->client->request();

        return $response->isSuccessful();
    }
}