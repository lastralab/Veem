<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 14/01/19
 * Time: 02:42 PM
 */

namespace Veem\Payment\Model;


use Magento\Framework\DataObject;
use Veem\Payment\Api\Data\InvoiceInterface;

class Invoice extends DataObject implements InvoiceInterface
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setData('id', $id);
        return $this;
    }

    /**
     * @return object
     */
    public function getAmount()
    {
        return $this->getData('amount');
    }

    /**
     * @param object $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->setData('amount', $amount);
        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->getData('attachments');
    }

    /**
     * @param array $attachments
     * @return $this
     */
    public function setAttachments($attachments)
    {
        $this->setData('attachments', $attachments);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getEmails()
    {
        return $this->getData('emails');
    }

    /**
     * @param string[] $emails
     * @return $this
     */
    public function setEmails($emails)
    {
        $this->setData('emails', $emails);
        return $this;
    }

    /**
     * @return string
     */
    public function getClaimLink()
    {
        return $this->getData('claimLink');
    }

    /**
     * @param string $claimLink
     * @return $this
     */
    public function setClaimLink($claimLink)
    {
        $this->setData('claimLink', $claimLink);
        return $this;
    }

    /**
     * @return string
     */
    public function getDueDate()
    {
        return $this->getData('dueDate');
    }

    /**
     * @param string $dueDate
     * @return $this
     */
    public function setDueDate($dueDate)
    {
        $this->setData('dueDate', $dueDate);
        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRateQuoteId()
    {
        return $this->getData('exchangeRateQuoteId');
    }

    /**
     * @param string $exchangeRateQuoteId
     * @return $this
     */
    public function setExchangeRateQuoteId($exchangeRateQuoteId)
    {
        $this->setData('exchangeRateQuoteId', $exchangeRateQuoteId);
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalInvoiceRefId()
    {
        return $this->getData('externalInvoiceRefId');
    }

    /**
     * @param string $externalInvoiceRefId
     * @return $this
     */
    public function setExternalInvoiceRefId($externalInvoiceRefId)
    {
        $this->setData('externalInvoiceRefId', $externalInvoiceRefId);
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->getData('notes');
    }

    /**
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->setData('notes', $notes);
        return $this;
    }

    /**
     * @return object
     */
    public function getPayer()
    {
        return $this->getData('payer');
    }

    /**
     * @param object $payer
     * @return $this
     */
    public function setPayer($payer)
    {
        $this->setData('payer', $payer);
        return $this;
    }

    /**
     * @return string
     */
    public function getPurposeOfPayment()
    {
        return $this->getData('purposeOfPayment');
    }

    /**
     * @param string $purposeOfPayment
     * @return $this
     */
    public function setPurposeOfPayment($purposeOfPayment)
    {
        $this->setData('purposeOfPayment', $purposeOfPayment);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData('status', $status);
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeCreated()
    {
        return $this->getData('timeCreated');
    }

    /**
     * @param string $timeCreated
     * @return $this
     */
    public function setTimeCreated($timeCreated)
    {
        $this->setData('timeCreated', $timeCreated);
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->getData('requestId');
    }
}