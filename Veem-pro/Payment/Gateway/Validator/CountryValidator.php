<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/12/18
 * Time: 03:47 PM
 */

namespace Veem\Payment\Gateway\Validator;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Veem\Payment\Model\CountryCurrencyMap;

class CountryValidator extends AbstractValidator
{
    private $config;
    private $countryCurrencyMap;

    public function __construct(ResultInterfaceFactory $resultFactory, ConfigInterface $config, CountryCurrencyMap $countryCurrencyMap)
    {
        $this->config = $config;
        $this->countryCurrencyMap = $countryCurrencyMap;
        parent::__construct($resultFactory);
    }

    public function validate(array $validationSubject)
    {
        $isValid = true;
        $storeId = $validationSubject['storeId'];

        if ((int)$this->config->getValue('allowspecific', $storeId) === 1) {
            $availableCountries = explode(
                ',',
                $this->config->getValue('specificcountry', $storeId)
            );

            if (!in_array($validationSubject['country'], $availableCountries)) {
                $isValid =  false;
            }
        }

        if($isValid) {
            $isValid = $this->countryCurrencyMap->isCountryAvailable($validationSubject['country']);
        }

        return $this->createResult($isValid);
    }
}