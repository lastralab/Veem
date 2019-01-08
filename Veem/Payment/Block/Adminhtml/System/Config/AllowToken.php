<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 19/12/18
 * Time: 03:54 PM
 */

namespace Veem\Payment\Block\Adminhtml\System\Config;


use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Gateway\ConfigInterface;

class AllowToken extends Template implements RendererInterface
{
    private $config;

    public function __construct(
        Template\Context $context,
        ConfigInterface $config,
        array $data = []
    )
    {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    public function checkConfig()
    {
        if (
            !empty($this->config->getValue('merchant_id')) &&
            !empty($this->config->getValue('merchant_secret'))
        ) {
            return 1;
        }

        return 0;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setValue($this->checkConfig());
        return $element->getDefaultHtml();
    }
}