<?php

namespace Veem\Payment\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CacheInterface;
use Veem\Payment\Gateway\Http\Client;
use Veem\Payment\Gateway\Http\TransferFactory;
use Magento\Framework\Serialize\Serializer\Json;

class CountryCurrencyMap
{
    const CACHE_TAG_CURRENCY_MAP = 'veem_currency_map_used';

    private $storeManager;
    private $httpClient;
    private $transferFactory;
    private $scopeConfig;
    private $cache;
    private $serializer;
    private $currentCurrencyCode;

    public function __construct(StoreManagerInterface $storeManager,
                                ScopeConfigInterface $scopeConfig,
                                CacheInterface $cache,
                                Client $httpClient,
                                TransferFactory $transferFactory,
                                Json $serializer)
    {
        $this->storeManager = $storeManager;
        $this->httpClient = $httpClient;
        $this->transferFactory = $transferFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->currentCurrencyCode = $this->getCurrentCurrencyCode();
    }

    public function isCurrencyAvailable($country, $currency)
    {
        $cacheMap = $this->getCacheMap();
        if(is_array($cacheMap)) {
            $key = array_search($country, array_column($cacheMap, 'country'));
            if($key!==false && isset($cacheMap[$key]['sendingCurrencies'])
                && in_array($currency,$cacheMap[$key]['sendingCurrencies']))
                return true;
            else
                return false;
        }
        return false;
    }

    public function isCountryAvailable($country)
    {
        $cacheMap = $this->getCacheMap();
        if(is_array($cacheMap)) {
            $key = array_search($country, array_column($cacheMap, 'country'));
            if ($key !== false)
                return true;
            else
                return false;
        }
        return false;
    }

    public function getCurrentCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    public function getCacheMap()
    {
        $cacheMap = $this->cache->load(self::CACHE_TAG_CURRENCY_MAP);
        if($cacheMap !== false){
            $cacheMap = $this->serializer->unserialize($cacheMap);
        }else{
            $cacheMap = $this->httpClient->placeRequest($this->transferFactory->create([]));
            $this->cache->save(
                $this->serializer->serialize($cacheMap),
                self::CACHE_TAG_CURRENCY_MAP,
                [],
                432000
            );
        }
        return $cacheMap;
    }
}