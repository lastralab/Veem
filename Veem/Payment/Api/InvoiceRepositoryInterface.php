<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 14/01/19
 * Time: 02:50 PM
 */

namespace Veem\Payment\Api;

use Veem\Payment\Api\Data\InvoiceInterface;

interface InvoiceRepositoryInterface
{
    /**
     * Gets an invoice by id
     * @param int $id
     * @return InvoiceInterface
     */
    public function get($id);

    /**
     * Cancels an invoice by id
     * @param int $id
     * @return void
     */
    public function cancel($id);
}