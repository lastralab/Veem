<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 21/12/18
 * Time: 01:10 PM
 */

namespace Veem\Payment\Controller\Adminhtml\Oauth;


use Magento\Backend\App\Action;
use Magento\Framework\App\Area;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Store\Model\App\Emulation;
use Veem\Payment\Model\Token;

class GenerateToken extends Action
{
    const ADMIN_RESOURCE = 'Veem_Payment::generate_token';
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var Token
     */
    protected $tokenizer;

    /**
     * @var Emulation
     */
    protected $emulator;

    public function __construct(
        Action\Context $context,
        ConfigInterface $config,
        Token $tokenizer,
        Emulation $emulator
    )
    {
        $this->config = $config;
        $this->tokenizer = $tokenizer;
        $this->emulator = $emulator;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var JsonResult $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $website = $this->_request->getParam('website');

        $scopeId = 0;

        $data = [];

        if(!empty($website)) {
            $scopeId = $website;
        }

        $this->emulator->startEnvironmentEmulation($scopeId, Area::AREA_ADMINHTML);
        $token = $this->tokenizer->get();
        $this->emulator->stopEnvironmentEmulation();

        if(!$token) {
            $msg =  __('Unable to Generate Token');

            $code = 500;
        } else {
            $msg = __('Token Generated Correctly');
            $code = 200;
        }

       $data['msg'] = $msg;

        $this->prepareResult($result, $code, $data);

        return $result;
    }

    protected function prepareResult(JsonResult $result, int $code, array $data = [])
    {
        $result->setHttpResponseCode($code);

        $result->setData($data);
    }
}