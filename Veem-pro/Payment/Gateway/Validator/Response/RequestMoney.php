<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 02:19 PM
 */

namespace Veem\Payment\Gateway\Validator\Response;


use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Veem\Payment\Gateway\Helper\Response;

class RequestMoney extends AbstractValidator
{
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);

        if ($this->_isSuccessfulTransaction($response)) {
            return $this->createResult(
                true,
                []
            );
        } else {
            return $this->createResult(
                false,
                [__('Gateway rejected the transaction.')]
            );
        }
    }

    private function _isSuccessfulTransaction(array $response)
    {
        return (isset($response[Response::VEEM_INVOICE_ID])
            && isset($response[Response::VEEM_CLAIM_LINK])) || isset($response[Response::SKIP_VALIDATION]);
    }
}