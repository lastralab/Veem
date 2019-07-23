<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 14/01/19
 * Time: 02:42 PM
 */

namespace Veem\Payment\Api\Data;


interface InvoiceInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return object
     */
    public function getAmount();

    /**
     * @param object $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * @return array
     */
    public function getAttachments();

    /**
     * @param array $attachments
     * @return $this
     */
    public function setAttachments($attachments);

    /**
     * @return string[]
     */
    public function getEmails();

    /**
     * @param string[] $emails
     * @return $this
     */
    public function setEmails($emails);

    /**
     * @return string
     */
    public function getClaimLink();

    /**
     * @param string $claimLink
     * @return $this
     */
    public function setClaimLink($claimLink);

    /**
     * @return string
     */
    public function getDueDate();

    /**
     * @param string $dueDate
     * @return $this
     */
    public function setDueDate($dueDate);

    /**
     * @return string
     */
    public function getExchangeRateQuoteId();

    /**
     * @param string $exchangeRateQuoteId
     * @return $this
     */
    public function setExchangeRateQuoteId($exchangeRateQuoteId);

    /**
     * @return string
     */
    public function getExternalInvoiceRefId();

    /**
     * @param string $externalInvoiceRefId
     * @return $this
     */
    public function setExternalInvoiceRefId($externalInvoiceRefId);

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes);

    /**
     * @return object
     */
    public function getPayer();

    /**
     * @param object $payer
     * @return $this
     */
    public function setPayer($payer);

    /**
     * @return string
     */
    public function getPurposeOfPayment();

    /**
     * @param string $purposeOfPayment
     * @return $this
     */
    public function setPurposeOfPayment($purposeOfPayment);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getTimeCreated();

    /**
     * @param string $timeCreated
     * @return $this
     */
    public function setTimeCreated($timeCreated);

    /**
     * @return string
     */
    public function getRequestId();
}