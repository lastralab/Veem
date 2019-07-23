<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 02:26 PM
 */

namespace Veem\Payment\Gateway\Helper;


class Response
{
    const VEEM_INVOICE_ID = 'id'; // Request Money response Identifier
    const VEEM_PAYMENT_ID = 'id'; // Send Money response Identifier
    const VEEM_CLAIM_LINK = 'claimLink';
    const VEEM_TXN_ID = 'requestId';
    const VEEM_STATUS = 'status';

    // Payer & Payee properties
    const VEEM_EMAIL = 'email';
    const VEEM_FNAME = 'firstName';
    const VEEM_LNAME = 'lastName';
    const VEEM_COUNTRY = 'countryCode';
    const VEEM_PHONE = 'phone';

    // Response Objects
    const VEEM_AMOUNT = 'amount';
    const VEEM_PAYER = 'payer';
    const VEEM_PAYEE = 'payee';

    // Amount properties
    const VEEM_AMOUNT_CURRENCY = 'currency';
    const VEEM_AMOUNT_NUMBER = 'number';

    const SKIP_VALIDATION = 'skip_validation'; // If Request Money should be placed manually


    // Veem Statuses
    const VEEM_STATUS_SENT = 'Sent';
    const VEEM_STATUS_CLAIMED = 'Claimed';
    const VEEM_STATUS_MARK_AS_PAID = 'MarkAsPaid';
    const VEEM_STATUS_CANCELLED = 'Cancelled';
    const VEEM_STATUS_CLOSED = 'Closed';
}