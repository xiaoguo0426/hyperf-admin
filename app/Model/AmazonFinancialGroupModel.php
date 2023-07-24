<?php

namespace App\Model;

/**
 * Class AmazonInventoryModel
 * @package App\Model
 * @property $id
 * @property $merchant_id
 * @property $merchant_store_id
 * @property $financial_event_group_id
 * @property $processing_status
 * @property $fund_transfer_status
 * @property $original_total_amount
 * @property $original_total_code
 * @property $converted_total_amount
 * @property $converted_total_code
 * @property $fund_transfer_date
 * @property $trace_id
 * @property $account_tail
 * @property $beginning_balance_amount
 * @property $beginning_balance_code
 * @property $financial_event_group_start
 * @property $financial_event_group_end
 * @property $created_at
 * @property $updated_at
 */
class AmazonFinancialGroupModel extends Model
{
    protected ?string $table = 'amazon_finance_group';
}