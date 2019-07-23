<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 6/02/19
 * Time: 03:09 PM
 */

namespace Veem\Payment\Model\Config\Backend;



class Encrypted extends \Magento\Config\Model\Config\Backend\Encrypted
{
    const ACCESS_TOKEN = 'payment/veem/access_token';

    protected $writer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\Config\Storage\Writer $writer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->writer = $writer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $encryptor, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        parent::beforeSave();

        if(!$this->_dataSaveAllowed) {
            return;
        }

        if($this->_config->isSetFlag(
            self::ACCESS_TOKEN,
            $this->getScope(),
            $this->getScopeId()
        )) {
            // Remove old token
            $this->writer->delete(self::ACCESS_TOKEN, $this->getScope(), $this->getScopeId());
        }
    }
}