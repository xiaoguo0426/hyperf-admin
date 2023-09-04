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
 * @property $sku
 * @property $fnsku
 * @property $asin
 * @property $product_name
 * @property $approval_date
 * @property $amazon_order_id
 * @property $reimbursement_id
 * @property $case_id
 * @property $reason
 * @property $condition
 * @property $currency_unit
 * @property $amount_per_unit
 * @property $amount_total
 * @property $quantity_reimbursed_cash
 * @property $quantity_reimbursed_inventory
 * @property $quantity_reimbursed_total
 * @property $original_reimbursement_id
 * @property $original_reimbursement_type
 * @property $created_at
 * @property $updated_at
 */
class AmazonReportFbaReimbursementsDataModel extends Model
{
    protected ?string $table = 'amazon_report_fba_reimbursement';
}
