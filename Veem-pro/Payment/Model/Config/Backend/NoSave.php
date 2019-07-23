<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 20/12/18
 * Time: 02:44 PM
 */

namespace Veem\Payment\Model\Config\Backend;


use Magento\Framework\App\Config\Value;

class NoSave extends Value
{
    protected $_dataSaveAllowed = false;
}