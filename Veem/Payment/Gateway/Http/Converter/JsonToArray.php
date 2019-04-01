<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 03:48 PM
 */

namespace Veem\Payment\Gateway\Http\Converter;


use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;

class JsonToArray implements ConverterInterface
{
    protected $serializer;

    public function __construct(Json $serializer)
    {
        $this->serializer = $serializer;
    }

    public function convert($response)
    {
        if(!is_string($response)) {
            throw new ConverterException(__('Wrong response type'));
        }

        return $this->serializer->unserialize($response);
    }
}