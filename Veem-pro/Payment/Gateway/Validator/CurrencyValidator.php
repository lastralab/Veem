<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/12/18
 * Time: 03:50 PM
 */

namespace Veem\Payment\Gateway\Validator;


use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Veem\Payment\Model\CountryCurrencyMap;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Payment\Model\Checks\CanUseForCountry\CountryProvider;

class CurrencyValidator extends AbstractValidator
{

    private $config;
    private $countryCurrencyMap;
    private $checkoutSession;
    private $countryProvider;

    public function __construct(ResultInterfaceFactory $resultFactory,
                                ConfigInterface $config,
                                CountryCurrencyMap $countryCurrencyMap,
                                CountryProvider $countryProvider,
                                CheckoutSession $checkoutSession)
    {
        $this->config = $config;
        $this->countryCurrencyMap = $countryCurrencyMap;
        $this->checkoutSession = $checkoutSession;
        $this->countryProvider = $countryProvider;
        parent::__construct($resultFactory);
    }

    public function validate(array $validationSubject)
    {
        $country = $this->countryProvider->getCountry($this->checkoutSession->getQuote());
        $isValid = $this->countryCurrencyMap->isCurrencyAvailable($country, $validationSubject['currency']);

        return $this->createResult($isValid);
    }
}