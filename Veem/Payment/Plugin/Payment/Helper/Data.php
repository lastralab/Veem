<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 29/01/19
 * Time: 07:45 PM
 */

namespace Veem\Payment\Plugin\Payment\Helper;


use Magento\Framework\App\Area;
use Magento\Payment\Model\InfoInterface;
use Magento\Store\Model\App\Emulation;

class Data
{
    protected $emulation;

    public function __construct(Emulation $emulation)
    {
        $this->emulation = $emulation;
    }

    public function aroundGetInfoBlockHtml(
        \Magento\Payment\Helper\Data $subject,
        callable  $proceed,
        InfoInterface $info,
        $storeId
    )
    {
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        $result = $proceed($info, $storeId);

        $this->emulation->stopEnvironmentEmulation();

        return $result;
    }
}