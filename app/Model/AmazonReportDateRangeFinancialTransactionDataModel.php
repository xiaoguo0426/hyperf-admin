<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Model;

/**
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $date
 * @property $settlement_id
 * @property $type
 * @property $order_id
 * @property $sku
 * @property $description
 * @property $quantity
 * @property $marketplace
 * @property $account_type
 * @property $fulfillment
 * @property $order_city
 * @property $order_state
 * @property $order_postal
 * @property $tax_collection_model
 * @property $product_sales
 * @property $product_sales_tax
 * @property $shipping_credits
 * @property $shipping_credits_tax
 * @property $gift_wrap_credits
 * @property $giftwrap_credits_tax
 * @property $regulatory_fee
 * @property $tax_on_regulatory_fee
 * @property $promotional_rebates
 * @property $promotional_rebates_tax
 * @property $marketplace_withheld_tax
 * @property $selling_fees
 * @property $fba_fees
 * @property $other_transaction_fees
 * @property $other
 * @property $total
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportDateRangeFinancialTransactionDataModel extends Model
{
    protected ?string $table = 'amazon_report_date_range_financial_transaction_data';
}
