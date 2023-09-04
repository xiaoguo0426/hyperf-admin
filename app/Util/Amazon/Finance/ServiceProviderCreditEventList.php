<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

use AmazonPHP\SellingPartner\Model\Finances\SolutionProviderCreditEvent;
use Hyperf\Collection\Collection;

class ServiceProviderCreditEventList extends FinanceBase
{
    public function run($financialEvents): bool
    {
        $collection = new Collection();
        /**
         * @var SolutionProviderCreditEvent $financialEvent
         */
        foreach ($financialEvents as $financialEvent) {
            $provider_transaction_type = $financialEvent->getProviderTransactionType() ?? '';
            $seller_order_id = $financialEvent->getSellerOrderId() ?? '';
            $market_place_id = $financialEvent->getMarketplaceId() ?? '';
            $marketplace_country_code = $financialEvent->getMarketplaceCountryCode() ?? '';
            $seller_id = $financialEvent->getSellerId() ?? '';
            $seller_store_name = $financialEvent->getSellerStoreName() ?? '';
            $provider_id = $financialEvent->getProviderId() ?? '';
            $provider_store_name = $financialEvent->getProviderStoreName() ?? '';

            $transactionAmount = $financialEvent->getTransactionAmount();
            $transaction_currency = '';
            $transaction_amount = 0.00;
            if (! is_null($transactionAmount)) {
                $transaction_currency = $transactionAmount->getCurrencyCode() ?? '';
                $transaction_amount = $transactionAmount->getCurrencyAmount() ?? 0.00;
            }

            $transactionCreationDate = $financialEvent->getTransactionCreationDate();
            $transaction_creation_date = '';
            if (! is_null($transactionCreationDate)) {
                $transaction_creation_date = $transactionCreationDate->format('Y-m-d H:i:s');
            }

            $collection->push([
                'merchant_id' => $this->merchant_id,
                'merchant_store_id' => $this->merchant_store_id,
                'provider_transaction_type' => $provider_transaction_type,
                'seller_order_id' => $seller_order_id,
                'market_place_id' => $market_place_id,
                'marketplace_country_code' => $marketplace_country_code,
                'seller_id' => $seller_id,
                'seller_store_name' => $seller_store_name,
                'provider_id' => $provider_id,
                'provider_store_name' => $provider_store_name,
                'transaction_currency' => $transaction_currency,
                'transaction_amount' => $transaction_amount,
                'transaction_creation_date' => $transaction_creation_date,
            ]);
        }
        return true;
    }
}
