<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\FBALiquidationEvent;
use Hyperf\Collection\Collection;

class FbaLiquidationEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var FBALiquidationEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $postedDate = $financialEvent->getPostedDate();
            $posted_date = '';
            if (! is_null($postedDate)) {
                $posted_date = $postedDate->format('Y-m-d H:i:s');
            }
            $original_removal_order_id = $financialEvent->getOriginalRemovalOrderId() ?? '';
            $liquidationProceedsAmount = $financialEvent->getLiquidationProceedsAmount();
            $liquidation_proceeds_currency = '';
            $liquidation_proceeds_amount = 0.00;
            if (! is_null($liquidationProceedsAmount)) {
                $liquidation_proceeds_currency = $liquidationProceedsAmount->getCurrencyCode() ?? '';
                $liquidation_proceeds_amount = $liquidationProceedsAmount->getCurrencyAmount() ?? 0.00;
            }
            $liquidationFeeAmount = $financialEvent->getLiquidationFeeAmount();
            $liquidation_fee_currency = '';
            $liquidation_fee_amount = 0.00;
            if (! is_null($liquidationFeeAmount)) {
                $liquidation_fee_currency = $liquidationFeeAmount->getCurrencyCode() ?? '';
                $liquidation_fee_amount = $liquidationFeeAmount->getCurrencyAmount() ?? 0.00;
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'posted_date' => $posted_date,
                'original_removal_order_id' => $original_removal_order_id,
                'liquidation_proceeds_currency' => $liquidation_proceeds_currency,
                'liquidation_proceeds_amount' => $liquidation_proceeds_amount,
                'liquidation_fee_currency' => $liquidation_fee_currency,
                'liquidation_fee_amount' => $liquidation_fee_amount,
            ]);
        }
        return true;
    }
}
