<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\FailedAdhocDisbursementEventList as FailedAdhocDisbursementEventListNew;
use Hyperf\Collection\Collection;

class FailedAdhocDisbursementEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var FailedAdhocDisbursementEventListNew $financialEvents
         */
        $funds_transfers_type = $financialEvents->getFundsTransfersType() ?? '';
        $transfer_id = $financialEvents->getTransferId() ?? '';
        $disbursement_id = $financialEvents->getDisbursementId() ?? '';
        $payment_disbursement_type = $financialEvents->getPaymentDisbursementType() ?? '';
        $status = $financialEvents->getStatus() ?? '';
        $transferAmount = $financialEvents->getTransferAmount();
        $transfer_currency = '';
        $transfer_amount = '';
        if (! is_null($transferAmount)) {
            $transfer_currency = $transferAmount->getCurrencyCode() ?? '';
            $transfer_amount = $transferAmount->getCurrencyAmount() ?? 0.00;
        }
        $postedDate = $financialEvents->getPostedDate();
        $posted_date = '';
        if (! is_null($postedDate)) {
            $posted_date = $postedDate->format('Y-m-d H:i:s');
        }
        if ($funds_transfers_type === '' && $transfer_id === '' && $disbursement_id === '' && $payment_disbursement_type === '') {
            return true;
        }

        $collection->push([
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'funds_transfers_type' => $funds_transfers_type,
            'transfer_id' => $transfer_id,
            'disbursement_id' => $disbursement_id,
            'payment_disbursement_type' => $payment_disbursement_type,
            'status' => $status,
            'transfer_currency' => $transfer_currency,
            'transfer_amount' => $transfer_amount,
            'posted_date' => $posted_date,
        ]);
        return true;
    }
}
