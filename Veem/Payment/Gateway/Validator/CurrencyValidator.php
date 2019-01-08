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

class CurrencyValidator extends AbstractValidator
{

    private $config;

    public function __construct(ResultInterfaceFactory $resultFactory, ConfigInterface $config)
    {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    public function validate(array $validationSubject)
    {
        // TODO: Implement validate() method.
        $isValid = true;

        return $this->createResult($isValid);
    }
}