<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 16/01/19
 * Time: 03:14 PM
 */

namespace Veem\Payment\Block;

use Magento\Framework\View\Element\Template;

class Form extends \Magento\Payment\Block\Form
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $countrySource;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var null|array
     */
    protected $countryOptions;

    public function __construct(
        Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $countrySource,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->countrySource = $countrySource;
        $this->directoryHelper = $directoryHelper;
    }

    public function getCountryOptions() {
        return $this->countrySource->toOptionArray();
    }

    public function getDirectoryCountryOptions() {
        if (!isset($this->countryOptions)) {
            $this->countryOptions = $this->getCountryOptions();
            $this->countryOptions = $this->orderCountryOptions($this->countryOptions);
        }

        return $this->countryOptions;
    }

    private function orderCountryOptions(array $countryOptions)
    {
        $topCountryCodes = $this->directoryHelper->getTopCountryCodes();
        if (empty($topCountryCodes)) {
            return $countryOptions;
        }

        $headOptions = [];
        $tailOptions = [[
            'value' => 'delimiter',
            'label' => '──────────',
            'disabled' => true,
        ]];
        foreach ($countryOptions as $countryOption) {
            if (empty($countryOption['value']) || in_array($countryOption['value'], $topCountryCodes)) {
                $headOptions[] = $countryOption;
            } else {
                $tailOptions[] = $countryOption;
            }
        }
        return array_merge($headOptions, $tailOptions);
    }
}