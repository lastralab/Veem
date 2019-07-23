<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 11:19 AM
 */

namespace Veem\Payment\Model;


use Magento\Payment\Gateway\Config\Config;
use Veem\Payment\Api\Data\UrlBuilderInterface;

class UrlBuilder implements UrlBuilderInterface
{
    const DS = DIRECTORY_SEPARATOR;

    protected $config;

    protected $uri;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function build()
    {
        $baseUrl = $this->config->getValue('sandbox_mode') ?
            static::SANDBOX_URI :
            static::LIVE_URI;

        return rtrim($baseUrl, static::DS) .
            static::DS .
            ltrim($this->uri, static::DS);
    }

    public function getUri()
    {
        return (string) $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }
}