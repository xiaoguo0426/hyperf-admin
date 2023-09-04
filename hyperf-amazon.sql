/*
 Navicat Premium Data Transfer

 Source Server         : laradock
 Source Server Type    : MySQL
 Source Server Version : 50739
 Source Host           : 127.0.0.1:3306
 Source Schema         : hyperf

 Target Server Type    : MySQL
 Target Server Version : 50739
 File Encoding         : 65001

 Date: 04/09/2023 19:29:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for amazon_app
-- ----------------------------
DROP TABLE IF EXISTS `amazon_app`;
CREATE TABLE `amazon_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '商户店铺ID',
  `seller_id` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊seller_id   merchant_store.unique_id',
  `app_id` varchar(128) NOT NULL DEFAULT '' COMMENT '应用程序 ID',
  `app_name` varchar(128) NOT NULL DEFAULT '' COMMENT '应用名称',
  `aws_access_key` varchar(128) NOT NULL DEFAULT '' COMMENT 'Aws Access Key',
  `aws_secret_key` varchar(128) NOT NULL DEFAULT '' COMMENT 'Aws Secret Key',
  `user_arn` varchar(255) NOT NULL DEFAULT '' COMMENT 'User ARN',
  `role_arn` varchar(255) NOT NULL DEFAULT '' COMMENT 'ROLE ARN',
  `lwa_client_id` varchar(255) NOT NULL DEFAULT '' COMMENT '客户端编码lwaClientId',
  `lwa_client_id_secret` varchar(255) NOT NULL DEFAULT '' COMMENT '客户端秘钥lwaClientIdSecret',
  `region` varchar(20) NOT NULL DEFAULT '' COMMENT '地区',
  `country_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '市场(国家二字码)(英文逗号分割)',
  `refresh_token` varchar(512) NOT NULL DEFAULT '' COMMENT '刷新令牌',
  `config` longtext COMMENT '不同地区与refresh token关系配置',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0禁用 1啓用',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_finance_group
-- ----------------------------
DROP TABLE IF EXISTS `amazon_finance_group`;
CREATE TABLE `amazon_finance_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `merchant_store_id` int(10) NOT NULL COMMENT '店铺id',
  `financial_event_group_id` varchar(128) NOT NULL DEFAULT '' COMMENT '财务事件组的唯一标识符',
  `processing_status` varchar(32) NOT NULL DEFAULT '' COMMENT '财务事件组的处理状态表示财务事件组的余额是否已结算。',
  `fund_transfer_status` varchar(32) NOT NULL DEFAULT '' COMMENT '资金转移的状态',
  `original_total_amount` varchar(20) NOT NULL DEFAULT '' COMMENT '发生交易的市场货币的总金额',
  `original_total_code` varchar(32) NOT NULL DEFAULT '' COMMENT '发生交易的市场货币的总金额货币',
  `converted_total_amount` varchar(10) NOT NULL DEFAULT '' COMMENT '支付资金的市场货币总额',
  `converted_total_code` varchar(32) NOT NULL DEFAULT '' COMMENT '支付资金的市场货币总额货币',
  `fund_transfer_date` varchar(20) NOT NULL DEFAULT '' COMMENT '开始支付或收费的日期和时间。仅适用于已关闭的事件组。采用 ISO 8601 日期时间格式。',
  `trace_id` varchar(255) NOT NULL DEFAULT '' COMMENT '卖家用来在外部查找交易的跟踪标识符',
  `account_tail` varchar(32) NOT NULL DEFAULT '' COMMENT '支付工具的账户尾数',
  `beginning_balance_amount` varchar(10) NOT NULL DEFAULT '' COMMENT '期初余额',
  `beginning_balance_code` varchar(32) NOT NULL DEFAULT '' COMMENT '期初余额货币',
  `financial_event_group_start` varchar(20) NOT NULL DEFAULT '' COMMENT '打开财务事件组的日期和时间。采用 ISO 8601 日期时间格式',
  `financial_event_group_end` varchar(20) NOT NULL DEFAULT '' COMMENT '财务事件组关闭的日期和时间。采用 ISO 8601 日期时间格式',
  `created_at` datetime DEFAULT NULL COMMENT '系统创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '系统更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COMMENT='亚马逊财务事件组';

-- ----------------------------
-- Table structure for amazon_finance_shipment_event_list
-- ----------------------------
DROP TABLE IF EXISTS `amazon_finance_shipment_event_list`;
CREATE TABLE `amazon_finance_shipment_event_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `amazon_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊订单id',
  `seller_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '卖方定义的订单标识符。',
  `marketplace_name` varchar(20) NOT NULL COMMENT '市场名称',
  `order_charge_list` varchar(128) NOT NULL COMMENT '订单收费列表',
  `order_charge_adjustment_list` varchar(128) NOT NULL COMMENT '订单费用调整列表',
  `shipment_fee_adjustment_list` varchar(128) NOT NULL COMMENT '运费调整列表',
  `order_fee_list` varchar(128) NOT NULL COMMENT '订单费列表',
  `order_fee_adjustment_list` varchar(128) NOT NULL COMMENT '订单费调整列表',
  `direct_payment_list` varchar(128) NOT NULL COMMENT '买家通过亚马逊提供的信用卡之一向亚马逊付款或买家直接通过COD向卖家付款的交易列表。',
  `posted_date` datetime DEFAULT NULL COMMENT '发布财务事件的日期和时间。',
  `shipment_item_list` varchar(255) NOT NULL COMMENT '装运物品清单',
  `shipment_item_adjustment_list` varchar(255) NOT NULL COMMENT '装运项目调整清单',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_inventory
-- ----------------------------
DROP TABLE IF EXISTS `amazon_inventory`;
CREATE TABLE `amazon_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `asin` varchar(128) NOT NULL DEFAULT '',
  `fn_sku` varchar(128) NOT NULL DEFAULT '',
  `seller_sku` varchar(128) NOT NULL DEFAULT '',
  `product_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `condition` varchar(20) NOT NULL DEFAULT '' COMMENT '商品状态，例如"NewItem"',
  `fulfillable_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '可履行数量',
  `inbound_working_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '运输中、待上架数量',
  `inbound_shipped_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '已运抵、待上架数量',
  `inbound_receiving_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '正在接收的数量',
  `total_reserved_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '总预留数量',
  `pending_customer_order_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '等待顾客订单数量',
  `pending_transshipment_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '等待转运数量',
  `fc_processing_quantity` int(11) NOT NULL DEFAULT '0' COMMENT 'FBA中心处理中数量',
  `total_researching_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '总调查数量',
  `researching_quantity_in_short_term` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '短期内调查数量',
  `researching_quantity_in_mid_term` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '中期内调查数量',
  `researching_quantity_in_long_term` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '长期内调查数量',
  `total_unfulfillable_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '总不可履行数量',
  `customer_damaged_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '顾客损坏数量',
  `warehouse_damaged_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '仓库损坏数量',
  `distributor_damaged_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '经销商损坏数量',
  `carrier_damaged_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '运输商损坏数量',
  `defective_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '有缺陷数量',
  `expired_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '已过期数量',
  `last_updated_time` datetime NOT NULL COMMENT '最后更新时间',
  `total_quantity` int(11) NOT NULL DEFAULT '0' COMMENT '总数量',
  `country_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '市场',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8mb4 COMMENT='amazon-库存';

-- ----------------------------
-- Table structure for amazon_order
-- ----------------------------
DROP TABLE IF EXISTS `amazon_order`;
CREATE TABLE `amazon_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `seller_id` varchar(128) NOT NULL DEFAULT '' COMMENT '卖家id',
  `amazon_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊定义的订单标识符，格式为3-7-7',
  `seller_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '卖家定义的订单标识符',
  `purchase_date` datetime DEFAULT NULL COMMENT '订单创建时间',
  `last_update_date` varchar(30) NOT NULL DEFAULT '' COMMENT '上次更新订单的日期',
  `order_status` varchar(50) NOT NULL DEFAULT '' COMMENT '订单状态 Pending,Unshipped,PartiallyShipped,Shipped,Canceled,Unfulfillable,InvoiceUnconfirmed,PendingAvailability',
  `fulfillment_channel` varchar(128) NOT NULL DEFAULT '' COMMENT '订单是由亚马逊（AFN）还是由卖方（MFN）完成     MFN,AFN',
  `sales_channel` varchar(128) NOT NULL DEFAULT '' COMMENT '订单中第一项的销售渠道',
  `order_channel` varchar(128) NOT NULL DEFAULT '' COMMENT '订单中第一项的订单通道',
  `ship_service_level` varchar(128) NOT NULL DEFAULT '' COMMENT '订单的发货服务级别',
  `order_total_currency` varchar(10) NOT NULL DEFAULT '' COMMENT '订单的总费用(货币)',
  `order_total_amount` varchar(20) NOT NULL DEFAULT '' COMMENT '订单的总费用',
  `number_of_items_shipped` tinyint(5) unsigned NOT NULL DEFAULT '0' COMMENT '装运的项目数',
  `number_of_items_unshipped` tinyint(5) unsigned NOT NULL DEFAULT '0' COMMENT '未装运的项目数',
  `payment_execution_detail` text NOT NULL COMMENT '关于货到付款（COD）订单的子付款方式的信息',
  `payment_method` varchar(128) NOT NULL DEFAULT '' COMMENT '订单的付款方式。COD,CVS,Other   此属性仅限于货到付款（COD）和便利店（CVS）付款方式。除非您需要PaymentExecutionDetailItem对象提供的特定COD付款信息，否则建议使用PaymentMethodDetails属性获取付款方式信息。',
  `payment_method_details` text NOT NULL COMMENT '订单的付款方式列表',
  `marketplace_id` varchar(128) NOT NULL DEFAULT '' COMMENT '下订单的市场的标识符',
  `shipment_service_level_category` varchar(128) NOT NULL DEFAULT '' COMMENT '订单的装运服务级别类别 Expedited, FreeEconomy, NextDay, SameDay, SecondDay, Scheduled, Standard.',
  `easy_ship_shipment_status` varchar(128) NOT NULL DEFAULT '' COMMENT 'Amazon Easy Ship订单的状态。此属性仅适用于Amazon Easy Ship订单。',
  `cba_displayable_shipping_label` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊（CBA）结账的定制发货标签',
  `order_type` varchar(128) NOT NULL DEFAULT '' COMMENT '订单类型 StandardOrder,LongLeadTimeOrder,Preorder,BackOrder,SourcingOnDemandOrder',
  `earliest_ship_date` varchar(30) NOT NULL DEFAULT '' COMMENT '您承诺发货订单的时间段的开始。采用ISO 8601日期时间格式。仅针对卖方完成的订单退回。',
  `latest_ship_date` varchar(30) NOT NULL DEFAULT '' COMMENT '您承诺发货订单的时间段结束',
  `earliest_delivery_date` varchar(30) NOT NULL DEFAULT '' COMMENT '您承诺履行订单的时间段的开始。采用ISO 8601日期时间格式。仅针对卖方完成的订单退回。',
  `latest_delivery_date` varchar(30) NOT NULL DEFAULT '' COMMENT '您承诺履行订单的期限结束。采用ISO 8601日期时间格式。仅针对卖家完成的订单返回，这些订单没有挂起可用性、挂起或取消状态',
  `is_business_order` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则订单为Amazon Business订单。亚马逊商业订单是指买方是经验证的商业买家的订单',
  `is_prime` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则订单是卖家完成的亚马逊Prime订单。',
  `is_premium_order` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则订单具有“高级配送服务级别协议”。有关高级配送订单的更多信息，请参阅您所在市场的卖家中心帮助中的“高级配送选项”',
  `is_global_express_enabled` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则订单为GlobalExpress订单',
  `replaced_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '正在替换的订单的订单ID值。仅当IsReplacementOrder=true时返回。',
  `is_replacement_order` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则这是替换订单。',
  `promise_response_due_date` varchar(128) NOT NULL DEFAULT '' COMMENT '表示卖方必须以预计发货日期回复买方的日期。仅针对按需采购订单退回。',
  `is_estimated_ship_date_set` varchar(128) NOT NULL DEFAULT '' COMMENT '如果为true，则为订单设置预计发货日期。仅针对按需采购订单退回',
  `is_sold_by_ab` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则此订单中的商品由Amazon Business EU SARL（ABEU）购买并转售。通过购买并立即转售您的物品，ABEU成为记录的卖家，使您的库存可供不从第三方卖家购买的客户出售。',
  `is_iba` varchar(128) NOT NULL DEFAULT '' COMMENT '如果为true，则此订单中的商品由Amazon Business EU SARL（ABEU）购买并转售。通过购买并立即转售您的物品，ABEU成为记录的卖家，使您的库存可供不从第三方卖家购买的客户出售。',
  `default_ship_from_location_address` text NOT NULL COMMENT '卖方装运物品的推荐地点。结账时计算。卖方可以选择或不选择从该地点发货',
  `buyer_invoice_preference` varchar(500) NOT NULL DEFAULT '' COMMENT '买方的发票偏好。仅在TR市场上可用',
  `buyer_tax_information` text NOT NULL COMMENT '包含业务发票税务信息',
  `fulfillment_instruction` text NOT NULL COMMENT '包含有关履行的说明，如从何处履行',
  `is_ispu` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则此订单标记为从商店提货，而不是交付',
  `is_access_point_order` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则将此订单标记为要交付给接入点。访问位置由客户选择。接入点包括亚马逊中心储物柜、亚马逊中心柜台和运营商运营的取货点。',
  `marketplace_tax_info` text NOT NULL COMMENT '有关市场的税务信息',
  `seller_display_name` varchar(128) NOT NULL DEFAULT '' COMMENT '卖家在市场上注册的友好名称',
  `shipping_address` text NOT NULL COMMENT '订单的发货地址',
  `buyer_email` varchar(255) NOT NULL DEFAULT '' COMMENT '买家email',
  `buyer_info` text NOT NULL COMMENT '买家信息',
  `automated_shipping_settings` text NOT NULL COMMENT '包含有关配送设置自动程序的信息，例如订单的配送设置是否自动生成，以及这些设置是什么',
  `has_regulated_items` varchar(255) NOT NULL DEFAULT '' COMMENT '订单是否包含在履行之前可能需要额外批准步骤的监管项目',
  `electronic_invoice_status` varchar(255) NOT NULL DEFAULT '' COMMENT '电子发票的状态 NotRequired,NotFound,Processing,Errored,Accepted',
  `is_ignore_evaluation` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否跳过索评 1是 0否',
  `created_at` datetime NOT NULL COMMENT '订单拉取入库时间(内部使用)',
  `updated_at` datetime NOT NULL COMMENT '订单最后一次更新入库时间(内部使用)',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `amazon_order_id_idx` (`amazon_order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37658 DEFAULT CHARSET=utf8mb4 COMMENT='亚马逊订单表';

-- ----------------------------
-- Table structure for amazon_order_items
-- ----------------------------
DROP TABLE IF EXISTS `amazon_order_items`;
CREATE TABLE `amazon_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商戶id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '商户店铺ID',
  `seller_id` varchar(128) NOT NULL DEFAULT '' COMMENT '卖家ID',
  `order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊订单id',
  `asin` varchar(128) NOT NULL DEFAULT '' COMMENT '物品的亚马逊标准标识号（ASIN）',
  `order_item_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Amazon定义的订单项标识符',
  `seller_sku` varchar(255) NOT NULL COMMENT '商品的卖方库存单位（SKU）',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '商品标题',
  `quantity_ordered` varchar(255) NOT NULL DEFAULT '' COMMENT '商品数量',
  `quantity_shipped` varchar(255) NOT NULL DEFAULT '' COMMENT '装运的商品数量',
  `product_info_number_of_items` varchar(255) NOT NULL DEFAULT '' COMMENT 'ASIN中包含的项目总数',
  `points_granted` text NOT NULL COMMENT '购买商品时获得的亚马逊积分的数量和价值',
  `item_price` text NOT NULL COMMENT '订单项的销售价格。请注意，订单项目是项目和数量。这意味着ItemPrice的值等于商品的售价乘以订购数量。请注意，ItemPrice不包括ShippingPrice和GiftWrapPrice。',
  `shipping_price` text NOT NULL COMMENT '项目的装运价格',
  `item_tax` text NOT NULL COMMENT '项目价格的税',
  `shipping_tax` text NOT NULL COMMENT '运费税',
  `shipping_discount` text NOT NULL COMMENT '运费折扣',
  `shipping_discount_tax` text NOT NULL COMMENT '运费折扣税',
  `promotion_discount` text NOT NULL COMMENT '优惠中所有促销折扣的总和',
  `promotion_discount_tax` text NOT NULL COMMENT '优惠中所有促销折扣总额的税收',
  `promotion_ids` text NOT NULL COMMENT '创建促销时由卖家提供的促销标识符列表',
  `cod_fee` text NOT NULL COMMENT 'COD服务费',
  `cod_fee_discount` text NOT NULL COMMENT 'COD费折扣',
  `is_gift` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则该物品是礼物',
  `condition_note` text NOT NULL COMMENT '卖方描述的物品状况',
  `condition_id` text NOT NULL COMMENT '项目的状态。可能的值：New新建、Used二手、Collectible可收藏、Refurbished翻新、Preorder预购、Club俱乐部',
  `condition_subtype_id` text NOT NULL COMMENT '项目的子条件',
  `scheduled_delivery_start_date` varchar(50) NOT NULL DEFAULT '' COMMENT '订单目的地时区中计划交货窗口的开始日期。采用ISO 8601日期时间格式',
  `scheduled_delivery_end_date` varchar(50) NOT NULL DEFAULT '' COMMENT '订单目的地时区中计划交货窗口的结束日期。采用ISO 8601日期时间格式',
  `price_designation` varchar(255) NOT NULL DEFAULT '' COMMENT '表示销售价格是仅适用于亚马逊业务订单的特殊价格',
  `tax_collection` text NOT NULL COMMENT '代扣税款信息',
  `serial_number_required` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则此项目的产品类型具有序列号。 仅亚马逊Easy Ship订单退回',
  `is_transparency` varchar(10) NOT NULL DEFAULT '' COMMENT '如果为true，则需要透明度代码',
  `ioss_number` varchar(128) NOT NULL DEFAULT '' COMMENT '市场的IOSS编号。从欧盟以外地区运往欧盟（EU）的卖家必须在亚马逊收取销售增值税后向其承运人提供此IOSS编号。',
  `store_chain_store_id` varchar(255) NOT NULL DEFAULT '' COMMENT '存储链存储标识符。链接到连锁店中的特定商店',
  `deemed_reseller_category` varchar(255) NOT NULL DEFAULT '' COMMENT '被视为经销商的类别。这适用于不在欧盟的销售合作伙伴，用于帮助他们符合欧盟和英国的增值税视同经销商税法。',
  `buyer_info` text NOT NULL COMMENT '单个项目的买家信息',
  `buyer_requested_cancel` text NOT NULL COMMENT '关于买方是否要求取消的信息。\n\n',
  `is_evaluation` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否索评 0未检测 1待索评 2索评失败 3已索评',
  `evaluation` varchar(255) NOT NULL DEFAULT '' COMMENT '索评结果',
  `is_estimated_fba_fee` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否为预估费用 0否 1是',
  `fba_fee` varchar(255) NOT NULL DEFAULT '' COMMENT 'fba费用',
  `fba_fee_currency` varchar(10) NOT NULL DEFAULT '' COMMENT 'fba费用(货币)',
  `commission` varchar(255) NOT NULL DEFAULT '' COMMENT '佣金',
  `commission_currency` varchar(10) NOT NULL DEFAULT '' COMMENT '佣金(货币)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_item_asin_idx` (`asin`) USING BTREE,
  KEY `order_item_order_id_idx` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34915 DEFAULT CHARSET=utf8mb4 COMMENT='亚马逊订单项表';

-- ----------------------------
-- Table structure for amazon_report_date_range_financial_transaction_data
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_date_range_financial_transaction_data`;
CREATE TABLE `amazon_report_date_range_financial_transaction_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `settlement_id` varchar(128) NOT NULL DEFAULT '' COMMENT '结算id',
  `type` varchar(50) NOT NULL DEFAULT '',
  `order_id` varchar(128) NOT NULL DEFAULT '',
  `sku` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(1000) NOT NULL DEFAULT '',
  `quantity` int(5) unsigned NOT NULL DEFAULT '0',
  `marketplace` varchar(50) NOT NULL DEFAULT '',
  `account_type` varchar(30) NOT NULL DEFAULT '',
  `fulfillment` varchar(128) NOT NULL DEFAULT '',
  `order_city` varchar(128) NOT NULL DEFAULT '',
  `order_state` varchar(128) NOT NULL DEFAULT '',
  `order_postal` varchar(128) NOT NULL DEFAULT '',
  `tax_collection_model` varchar(128) NOT NULL DEFAULT '',
  `product_sales` varchar(10) NOT NULL DEFAULT '',
  `product_sales_tax` varchar(10) NOT NULL DEFAULT '',
  `shipping_credits` varchar(10) NOT NULL DEFAULT '',
  `shipping_credits_tax` varchar(10) NOT NULL DEFAULT '',
  `gift_wrap_credits` varchar(10) NOT NULL DEFAULT '',
  `giftwrap_credits_tax` varchar(10) NOT NULL DEFAULT '',
  `regulatory_fee` varchar(10) NOT NULL DEFAULT '',
  `tax_on_regulatory_fee` varchar(10) NOT NULL DEFAULT '',
  `promotional_rebates` varchar(10) NOT NULL DEFAULT '',
  `promotional_rebates_tax` varchar(10) NOT NULL DEFAULT '',
  `marketplace_withheld_tax` varchar(10) NOT NULL DEFAULT '',
  `selling_fees` varchar(10) NOT NULL DEFAULT '',
  `fba_fees` varchar(10) NOT NULL DEFAULT '',
  `other_transaction_fees` varchar(10) NOT NULL DEFAULT '',
  `other` varchar(10) NOT NULL DEFAULT '',
  `total` varchar(10) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_report_date_range_financial_transaction_data_copy1
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_date_range_financial_transaction_data_copy1`;
CREATE TABLE `amazon_report_date_range_financial_transaction_data_copy1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `settlement_id` varchar(128) NOT NULL DEFAULT '' COMMENT '结算id',
  `type` varchar(50) NOT NULL DEFAULT '',
  `order_id` varchar(128) NOT NULL DEFAULT '',
  `sku` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(1000) NOT NULL DEFAULT '',
  `quantity` int(5) unsigned NOT NULL DEFAULT '0',
  `marketplace` varchar(50) NOT NULL DEFAULT '',
  `account_type` varchar(30) NOT NULL DEFAULT '',
  `fulfillment` varchar(128) NOT NULL DEFAULT '',
  `order_city` varchar(128) NOT NULL DEFAULT '',
  `order_state` varchar(128) NOT NULL DEFAULT '',
  `order_postal` varchar(128) NOT NULL DEFAULT '',
  `tax_collection_model` varchar(128) NOT NULL DEFAULT '',
  `product_sales` varchar(10) NOT NULL DEFAULT '',
  `product_sales_tax` varchar(10) NOT NULL DEFAULT '',
  `shipping_credits` varchar(10) NOT NULL DEFAULT '',
  `shipping_credits_tax` varchar(10) NOT NULL DEFAULT '',
  `gift_wrap_credits` varchar(10) NOT NULL DEFAULT '',
  `giftwrap_credits_tax` varchar(10) NOT NULL DEFAULT '',
  `regulatory_fee` varchar(10) NOT NULL DEFAULT '',
  `tax_on_regulatory_fee` varchar(10) NOT NULL DEFAULT '',
  `promotional_rebates` varchar(10) NOT NULL DEFAULT '',
  `promotional_rebates_tax` varchar(10) NOT NULL DEFAULT '',
  `marketplace_withheld_tax` varchar(10) NOT NULL DEFAULT '',
  `selling_fees` varchar(10) NOT NULL DEFAULT '',
  `fba_fees` varchar(10) NOT NULL DEFAULT '',
  `other_transaction_fees` varchar(10) NOT NULL DEFAULT '',
  `other` varchar(10) NOT NULL DEFAULT '',
  `total` varchar(10) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=63301 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_report_fba_estimated_fee
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_fba_estimated_fee`;
CREATE TABLE `amazon_report_fba_estimated_fee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT ' ',
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) unsigned NOT NULL,
  `country_id` varchar(5) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '国家二字码',
  `sku` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `fn_sku` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `asin` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `product_name` varchar(512) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `product_group` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `brand` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `fulfilled_by` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `your_price` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `sales_price` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `longest_side` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `median_side` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `shortest_side` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `length_and_girth` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `unit_of_dimension` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `item_package_weight` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `unit_of_weight` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `product_size_tier` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `currency` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '货币',
  `estimated_fee_total` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `estimated_referral_fee_per_unit` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `estimated_wariable_closing_fee` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `estimated_order_handing_fee_per_order` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `estimated_pick_pack_fee_per_unit` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `estimated_variable_closing_fee` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `estimated_weight_handling_fee_per_unit` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `expected_fulfillment_fee_per_unit` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '每单位预期履约费',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for amazon_report_fba_fulfillment_customer_return_data
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_fba_fulfillment_customer_return_data`;
CREATE TABLE `amazon_report_fba_fulfillment_customer_return_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) NOT NULL,
  `return_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `order_id` varchar(128) NOT NULL DEFAULT '',
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fnsku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `asin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `product_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fulfillment_center_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `detailed_disposition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `license_plate_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `customer_comments` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL COMMENT '数据首次拉取时间',
  `updated_at` datetime NOT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=394 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_report_fba_inventory_planning_data
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_fba_inventory_planning_data`;
CREATE TABLE `amazon_report_fba_inventory_planning_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned DEFAULT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned DEFAULT NULL COMMENT '店铺id',
  `snapshot_date` datetime NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fnsku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `asin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `product_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `condition` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `available` int(10) unsigned NOT NULL DEFAULT '0',
  `pending_removal_quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `inv_age_0_to_90_days` int(10) unsigned NOT NULL DEFAULT '0',
  `inv_age_91_to_180_days` int(10) unsigned NOT NULL DEFAULT '0',
  `inv_age_181_to_270_days` int(10) unsigned NOT NULL DEFAULT '0',
  `inv_age_271_to_365_days` int(10) unsigned NOT NULL DEFAULT '0',
  `inv_age_365_plus_days` int(10) unsigned NOT NULL DEFAULT '0',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `units_shipped_t7` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `units_shipped_t30` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `units_shipped_t60` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `units_shipped_t90` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alert` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `your_price` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sales_price` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lowest_price_new_plus_shipping` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lowest_price_used` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recommended_action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `healthy_inventory_level` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recommended_sales_price` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recommended_sale_duration_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recommended_removal_quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_cost_savings_of_recommended_actions` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sell_through` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `item_volume` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `volume_unit_measurement` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `storage_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `storage_volume` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `marketplace` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `product_group` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sales_rank` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `days_of_supply` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_excess_quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `weeks_of_cover_t30` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `weeks_of_cover_t90` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `featuredoffer_price` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sales_shipped_last_7_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sales_shipped_last_30_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sales_shipped_last_60_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sales_shipped_last_90_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inv_age_0_to_30_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inv_age_31_to_60_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inv_age_61_to_90_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inv_age_181_to_330_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inv_age_331_to_365_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_storage_cost_next_month` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inbound_quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inbound_working` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inbound_shipped` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inbound_received` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `no_sale_last_6_months` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reserved_quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `unfulfillable_quantity` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_181_210_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_181_210_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_211_240_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_211_240_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_241_270_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_241_270_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_271_300_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_271_300_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_301_330_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_301_330_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_331_365_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_331_365_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_to_be_charged_ais_365_plus_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `estimated_ais_365_plus_days` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL COMMENT '数据首次拉取时间',
  `updated_at` datetime NOT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for amazon_report_fba_myi_unsuppressed_inventory_data
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_fba_myi_unsuppressed_inventory_data`;
CREATE TABLE `amazon_report_fba_myi_unsuppressed_inventory_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) NOT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fnsku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `asin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `product_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `condition` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `your_price` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `mfn_listing_exists` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `mfn_fulfillable_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_listing_exists` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_warehouse_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_fulfillable_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_unsellable_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_reserved_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_total_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `per_unit_volume` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_inbound_working_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_inbound_shipped_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_inbound_receiving_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_researching_quantity` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_reserved_future_supply` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `afn_future_supply_buyable` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL COMMENT '数据首次拉取时间',
  `updated_at` datetime NOT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_report_fba_reimbursement
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_fba_reimbursement`;
CREATE TABLE `amazon_report_fba_reimbursement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `merchant_store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `sku` varchar(128) NOT NULL DEFAULT '' COMMENT 'SKU',
  `fnsku` varchar(128) NOT NULL DEFAULT '' COMMENT 'FNSKU',
  `asin` varchar(32) NOT NULL DEFAULT '' COMMENT 'ASIN',
  `product_name` varchar(255) NOT NULL DEFAULT '' COMMENT '产品名称',
  `approval_date` datetime DEFAULT NULL COMMENT '日期',
  `amazon_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '亚马逊订单ID',
  `reimbursement_id` varchar(128) NOT NULL DEFAULT '' COMMENT '报销编号',
  `case_id` varchar(128) NOT NULL DEFAULT '' COMMENT '问题编号',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '原因',
  `condition` varchar(128) NOT NULL DEFAULT '' COMMENT '状况',
  `currency_unit` varchar(32) NOT NULL DEFAULT '' COMMENT '货币单位',
  `amount_per_unit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每件商品的金额',
  `amount_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `quantity_reimbursed_cash` int(10) NOT NULL DEFAULT '0' COMMENT '赔偿数量【现金】',
  `quantity_reimbursed_inventory` varchar(10) NOT NULL DEFAULT '' COMMENT '赔偿数量【库存】',
  `quantity_reimbursed_total` int(10) NOT NULL DEFAULT '0' COMMENT '赔偿数量【总计】',
  `original_reimbursement_id` varchar(128) NOT NULL DEFAULT '' COMMENT '原始赔偿编号',
  `original_reimbursement_type` varchar(128) NOT NULL DEFAULT '' COMMENT '原始赔偿类型',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='亚马逊赔偿报告';

-- ----------------------------
-- Table structure for amazon_report_sales_and_traffic_by_asin
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_sales_and_traffic_by_asin`;
CREATE TABLE `amazon_report_sales_and_traffic_by_asin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `marketplace_id` varchar(32) NOT NULL COMMENT '市场',
  `data_time` datetime DEFAULT NULL COMMENT '数据时间',
  `parent_asin` varchar(32) NOT NULL DEFAULT '' COMMENT '(父)ASIN',
  `child_asin` varchar(32) NOT NULL DEFAULT '' COMMENT '(子)ASIN',
  `units_ordered` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已订购商品数',
  `units_ordered_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已订购商品数-B2B',
  `ordered_product_sales_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已订购商品销售额',
  `ordered_product_sales_currency_code` varchar(20) NOT NULL DEFAULT '' COMMENT '已订购商品销售额(货币)',
  `ordered_product_sales_b2b_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已订购商品销售额-B2B',
  `ordered_product_sales_b2b_amount_currency_code` varchar(20) NOT NULL DEFAULT '' COMMENT '已订购商品销售额-B2B(货币)',
  `total_order_items` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品总数',
  `total_order_items_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品总数-B2B',
  `browser_sessions` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器会话次数',
  `browser_sessions_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器会话次数-B2B',
  `mobile_app_sessions` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP会话次数',
  `mobile_app_sessions_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP会话次数-B2B',
  `sessions` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会话次数-总计',
  `sessions_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会话次数-总计-B2B',
  `browser_session_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '浏览器会话次数百分比',
  `browser_session_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '浏览器会话次数百分比-B2B',
  `mobile_app_session_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '手机APP会话次数百分比',
  `mobile_app_session_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '手机APP会话次数百分比-B2B',
  `session_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '会话百分比-总计',
  `session_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '会话百分比-总计-B2B',
  `browser_page_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器页面浏览量',
  `browser_page_views_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器页面浏览量-B2B',
  `mobile_app_page_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP页面浏览量',
  `mobile_app_page_views_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP页面浏览量-B2B',
  `page_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '页面浏览量-总计',
  `page_views_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '页面浏览量-总计-B2B',
  `browser_page_views_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '浏览器页面浏览量百分比',
  `browser_page_views_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '浏览器页面浏览量百分比-B2B',
  `mobile_app_page_views_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '手机APP页面浏览量百分比',
  `mobile_app_page_views_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '手机APP页面浏览量百分比-B2B',
  `page_views_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '页面浏览量-总计百分比',
  `page_views_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '页面浏览量-总计百分比-B2B',
  `buy_box_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '推荐报价（购买按钮）百分比',
  `buy_box_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '推荐报价（购买按钮）百分比 – B2B',
  `unit_session_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品会话百分比',
  `unit_session_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品会话百分比-B2B',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `multi_index` (`merchant_id`,`merchant_store_id`,`marketplace_id`,`data_time`,`parent_asin`,`child_asin`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7199 DEFAULT CHARSET=utf8mb4 COMMENT='亚马逊-销售与流量(ASIN)';

-- ----------------------------
-- Table structure for amazon_report_sales_and_traffic_by_date
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_sales_and_traffic_by_date`;
CREATE TABLE `amazon_report_sales_and_traffic_by_date` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `marketplace_id` varchar(32) NOT NULL COMMENT '市场',
  `data_time` datetime DEFAULT NULL COMMENT '数据时间',
  `ordered_product_sales_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已订购商品销售总额',
  `ordered_product_sales_currency_code` varchar(20) NOT NULL DEFAULT '' COMMENT '已订购商品销售总额(货币)',
  `ordered_product_sales_b2b_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已订购商品销售总额-B2B',
  `ordered_product_sales_b2b_currency_code` varchar(20) NOT NULL DEFAULT '' COMMENT '已订购商品销售总额-B2B(货币)',
  `units_ordered` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已订购商品数',
  `units_ordered_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已订购商品数-B2B',
  `total_order_items` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品总数',
  `total_order_items_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品总数-B2B',
  `average_sales_per_order_item_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `average_sales_per_order_item_currency_code` varchar(20) NOT NULL DEFAULT '',
  `average_sales_per_order_item_b2b_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `average_sales_per_order_item_b2b_currency_code` varchar(20) NOT NULL DEFAULT '',
  `average_units_per_order_item` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `average_units_per_order_item_b2b` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `average_selling_price_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `average_selling_price_currency_code` varchar(20) NOT NULL DEFAULT '',
  `average_selling_price_b2b_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `average_selling_price_b2b_currency_code` varchar(20) NOT NULL DEFAULT '',
  `units_refunded` int(10) unsigned NOT NULL DEFAULT '0',
  `refund_rate` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `claims_granted` int(10) unsigned NOT NULL DEFAULT '0',
  `claims_amount_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `claims_amount_currency_code` varchar(20) NOT NULL DEFAULT '',
  `shipped_product_sales_amount` double(10,2) unsigned NOT NULL DEFAULT '0.00',
  `shipped_product_sales_currency_code` varchar(20) NOT NULL DEFAULT '',
  `units_shipped` int(10) unsigned NOT NULL DEFAULT '0',
  `orders_shipped` int(10) unsigned NOT NULL DEFAULT '0',
  `browser_page_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器页面浏览量',
  `browser_page_views_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器页面浏览量-B2B',
  `mobile_app_page_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP页面浏览量',
  `mobile_app_page_views_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP页面浏览量-B2B',
  `page_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '页面浏览量-总计',
  `page_views_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '页面浏览量-总计-B2B',
  `browser_sessions` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器会话次数',
  `browser_sessions_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器会话次数-B2B',
  `mobile_app_sessions` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP会话次数',
  `mobile_app_sessions_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '手机APP会话次数—B2B',
  `sessions` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会话次数-总计',
  `sessions_b2b` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会话次数-总计-B2B',
  `buy_box_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '推荐报价（购买按钮）百分比',
  `buy_box_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '推荐报价（购买按钮）百分比 – B2B',
  `order_item_session_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00',
  `order_item_session_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00',
  `unit_session_percentage` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品会话百分比',
  `unit_session_percentage_b2b` double(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品会话百分比-B2B',
  `average_offer_count` int(10) unsigned NOT NULL DEFAULT '0',
  `average_parent_items` int(10) unsigned NOT NULL DEFAULT '0',
  `feedback_received` int(10) unsigned NOT NULL DEFAULT '0',
  `negative_feedback_received` int(10) unsigned NOT NULL DEFAULT '0',
  `received_negative_feedback_rate` double(5,2) unsigned NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `multi_idx` (`merchant_id`,`merchant_store_id`,`marketplace_id`,`data_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=776 DEFAULT CHARSET=utf8mb4 COMMENT='亚马逊-销售与流量(DATE)';

-- ----------------------------
-- Table structure for amazon_report_settlement_report_data_flat_file_v2
-- ----------------------------
DROP TABLE IF EXISTS `amazon_report_settlement_report_data_flat_file_v2`;
CREATE TABLE `amazon_report_settlement_report_data_flat_file_v2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) unsigned NOT NULL,
  `settlement_id` varchar(128) NOT NULL DEFAULT '' COMMENT '结算id',
  `settlement_start_date` datetime DEFAULT NULL COMMENT '结算开始时间',
  `settlement_end_date` datetime DEFAULT NULL COMMENT '结算结束时间',
  `deposit_date` datetime DEFAULT NULL COMMENT '存款时间',
  `total_amount` varchar(128) NOT NULL DEFAULT '' COMMENT '总数',
  `currency` varchar(128) NOT NULL DEFAULT '' COMMENT '货币',
  `transaction_type` varchar(128) NOT NULL DEFAULT '' COMMENT '交易类型',
  `order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '订单ID',
  `merchant_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '商户订单ID',
  `adjustment_id` varchar(128) NOT NULL DEFAULT '' COMMENT '调整ID',
  `shipment_id` varchar(128) NOT NULL DEFAULT '' COMMENT '装运ID',
  `marketplace_name` varchar(128) NOT NULL DEFAULT '' COMMENT '市场名称',
  `amount_type` varchar(128) NOT NULL DEFAULT '' COMMENT '金额类型',
  `amount_description` varchar(128) NOT NULL DEFAULT '' COMMENT '金额描述',
  `amount` varchar(128) NOT NULL DEFAULT '' COMMENT '金额',
  `fulfillment_id` varchar(128) NOT NULL DEFAULT '' COMMENT '履行ID',
  `posted_date` varchar(128) NOT NULL DEFAULT '' COMMENT '发布日期',
  `posted_date_time` varchar(128) DEFAULT '' COMMENT '发布日期时间',
  `order_item_code` varchar(128) NOT NULL DEFAULT '' COMMENT '订单商品代码',
  `merchant_order_item_id` varchar(128) NOT NULL DEFAULT '' COMMENT '商户订单项ID',
  `merchant_adjustment_item_id` varchar(128) NOT NULL DEFAULT '' COMMENT '商户调整项ID',
  `sku` varchar(128) NOT NULL DEFAULT '' COMMENT 'SKU',
  `quantity_purchased` varchar(128) NOT NULL DEFAULT '' COMMENT '购买数量',
  `promotion_id` varchar(128) NOT NULL DEFAULT '' COMMENT '促销ID',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=93913 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for amazon_sales_order_metrics
-- ----------------------------
DROP TABLE IF EXISTS `amazon_sales_order_metrics`;
CREATE TABLE `amazon_sales_order_metrics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `marketplace_id` varchar(32) NOT NULL COMMENT '市场',
  `interval_type` varchar(10) NOT NULL DEFAULT '' COMMENT '时间间隔类型 hour/day',
  `interval` datetime NOT NULL COMMENT '时间间隔',
  `unit_count` int(10) NOT NULL COMMENT '单位数',
  `order_count` int(10) NOT NULL COMMENT ' 订单数',
  `order_item_count` int(10) NOT NULL COMMENT '订单项数',
  `avg_unit_price_currency_code` varchar(10) NOT NULL COMMENT '平均单位价格(货币)',
  `avg_unit_price` varchar(20) NOT NULL COMMENT '平均单位价格',
  `total_sales_currency_code` varchar(10) NOT NULL COMMENT '销售总额(货币)',
  `total_sales_amount` varchar(20) NOT NULL COMMENT '销售总额',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `interval_idx` (`merchant_id`,`merchant_store_id`,`marketplace_id`,`interval_type`,`interval`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2169 DEFAULT CHARSET=utf8mb4 COMMENT='amazon-销售订单指标';

-- ----------------------------
-- Table structure for amazon_sales_order_metrics_asin
-- ----------------------------
DROP TABLE IF EXISTS `amazon_sales_order_metrics_asin`;
CREATE TABLE `amazon_sales_order_metrics_asin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `marketplace_id` varchar(32) NOT NULL COMMENT '市场',
  `asin` varchar(32) NOT NULL COMMENT 'ASIN',
  `interval_type` varchar(10) NOT NULL DEFAULT '' COMMENT '时间间隔类型 hour/day',
  `interval` datetime NOT NULL COMMENT '时间间隔',
  `unit_count` int(10) NOT NULL COMMENT '单位数',
  `order_count` int(10) NOT NULL COMMENT ' 订单数',
  `order_item_count` int(10) NOT NULL COMMENT '订单项数',
  `avg_unit_price_currency_code` varchar(10) NOT NULL COMMENT '平均单位价格(货币)',
  `avg_unit_price` varchar(20) NOT NULL COMMENT '平均单位价格',
  `total_sales_currency_code` varchar(10) NOT NULL COMMENT '销售总额(货币)',
  `total_sales_amount` varchar(20) NOT NULL COMMENT '销售总额',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `interval_idx` (`merchant_id`,`merchant_store_id`,`marketplace_id`,`interval_type`,`interval`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='amazon-asin销售订单指标';

-- ----------------------------
-- Table structure for amazon_shipments
-- ----------------------------
DROP TABLE IF EXISTS `amazon_shipments`;
CREATE TABLE `amazon_shipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `merchant_store_id` int(10) unsigned NOT NULL,
  `marketplace_id` varchar(50) NOT NULL DEFAULT '',
  `shipment_id` varchar(255) NOT NULL DEFAULT '',
  `shipment_name` varchar(255) NOT NULL DEFAULT '',
  `shipment_from_address` varchar(512) NOT NULL DEFAULT '',
  `destination_fulfillment_center_id` varchar(128) NOT NULL DEFAULT '',
  `shipment_status` varchar(50) NOT NULL DEFAULT '',
  `label_prep_type` varchar(50) NOT NULL DEFAULT '',
  `are_cases_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `confirmed_need_by_date` varchar(20) NOT NULL DEFAULT '',
  `box_contents_source` varchar(50) NOT NULL DEFAULT '',
  `total_units` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_per_unit_currency` varchar(10) NOT NULL DEFAULT '',
  `fee_per_unit_value` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `total_fee_currency` varchar(10) NOT NULL DEFAULT '',
  `total_fee_value` float(10,2) unsigned NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `last_updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipment_id_UNIQUE` (`shipment_id`),
  KEY `marketplace_id` (`marketplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for exchange_rate
-- ----------------------------
DROP TABLE IF EXISTS `exchange_rate`;
CREATE TABLE `exchange_rate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `base_currency` varchar(32) NOT NULL DEFAULT '' COMMENT '基础货币',
  `quote_currency` varchar(32) NOT NULL DEFAULT '' COMMENT '引用货币',
  `timestamp` int(10) unsigned NOT NULL COMMENT '时间(UTC)',
  `value` varchar(10) NOT NULL DEFAULT '' COMMENT '值',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `logo` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'logo',
  `images` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品图片',
  `desc` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品简述',
  `contents` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品内容',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for goods_attr_key
-- ----------------------------
DROP TABLE IF EXISTS `goods_attr_key`;
CREATE TABLE `goods_attr_key` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `key` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for goods_attr_val
-- ----------------------------
DROP TABLE IF EXISTS `goods_attr_val`;
CREATE TABLE `goods_attr_val` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `value` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '属性值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for goods_sku
-- ----------------------------
DROP TABLE IF EXISTS `goods_sku`;
CREATE TABLE `goods_sku` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `key_id` int(10) unsigned NOT NULL COMMENT '属性id',
  `value_id` int(10) unsigned NOT NULL COMMENT '属性值id',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'sku图片',
  `price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'sku价格，单位分',
  `status` smallint(5) unsigned NOT NULL COMMENT '状态 0禁用 1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for lazada_config
-- ----------------------------
DROP TABLE IF EXISTS `lazada_config`;
CREATE TABLE `lazada_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `region` varchar(20) NOT NULL DEFAULT '' COMMENT '地区',
  `app_key` varchar(128) NOT NULL DEFAULT '' COMMENT '应用KEY',
  `app_secret` varchar(128) NOT NULL DEFAULT '' COMMENT '应用秘钥',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0禁用 1启用',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL COMMENT '创建人',
  `updated_by` int(10) unsigned NOT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for lazada_order
-- ----------------------------
DROP TABLE IF EXISTS `lazada_order`;
CREATE TABLE `lazada_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '订单id',
  `order_number` varchar(128) NOT NULL DEFAULT '' COMMENT '订单ID',
  `branch_number` varchar(256) NOT NULL DEFAULT '' COMMENT '（仅限泰国）企业客户的税务局代码，由客户在下单时提供。',
  `tax_code` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '（仅限泰国和越南）客户的增值税税号，由客户在下单时提供。',
  `extra_attributes` varchar(2000) NOT NULL DEFAULT '' COMMENT '通过getMarketPlaceOrders调用传递给卖家中心的额外属性',
  `shipping_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '这个订单的运费总额。',
  `customer_first_name` varchar(128) NOT NULL DEFAULT '' COMMENT '客户名',
  `payment_method` varchar(255) NOT NULL DEFAULT '' COMMENT '付款方式',
  `statuses` varchar(1000) NOT NULL DEFAULT '' COMMENT '订单中项目的唯一状态数组',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `voucher` varchar(128) NOT NULL DEFAULT '' COMMENT '此订单的总凭证。',
  `national_registration_number` varchar(128) NOT NULL DEFAULT '' COMMENT '国家注册号',
  `promised_shipping_times` varchar(128) NOT NULL DEFAULT '' COMMENT '目标运输时间为最快的订单项目，如果他们是可用的。',
  `items_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '按顺序排列的项目数。',
  `voucher_platform` varchar(30) NOT NULL DEFAULT '' COMMENT 'lazada发放的代金券',
  `voucher_seller` varchar(255) NOT NULL DEFAULT '' COMMENT '卖家出具的凭证',
  `price` varchar(20) NOT NULL DEFAULT '' COMMENT '此订单的总金额。不是订单的最终交易价格，不包括凭证和shipping_fee',
  `address_billing` varchar(1000) NOT NULL DEFAULT '' COMMENT '计费地址',
  `warehouse_code` varchar(128) NOT NULL DEFAULT '' COMMENT '多卖家仓库代码',
  `shipping_fee_original` varchar(20) NOT NULL DEFAULT '' COMMENT '在任何类型的运费促销之前，应向客户收取的原始运费',
  `shipping_fee_discount_seller` varchar(20) NOT NULL DEFAULT '' COMMENT '卖家运费折扣',
  `shipping_fee_discount_platform` varchar(20) NOT NULL DEFAULT '' COMMENT '平台运费折扣',
  `address_shipping` varchar(1000) NOT NULL DEFAULT '' COMMENT '发货地址',
  `customer_last_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Empty for now. See cutomer_first_name.',
  `gift_option` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '如果物品是礼物，则为1；如果不是，则为0。',
  `voucher_code` varchar(128) NOT NULL DEFAULT '' COMMENT '凭证id',
  `delivery_info` varchar(128) NOT NULL DEFAULT '' COMMENT '交货信息',
  `gift_message` varchar(255) NOT NULL DEFAULT '' COMMENT '客户指定的礼品信息',
  `created_at` datetime NOT NULL COMMENT '下单的日期和时间。',
  `updated_at` datetime NOT NULL COMMENT '最后一次更改订单的日期和时间。',
  `pull_created_at` datetime NOT NULL COMMENT 'API首次拉取数据时间',
  `pull_updated_at` datetime NOT NULL COMMENT 'API最后一次拉取数据时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for lazada_order_items
-- ----------------------------
DROP TABLE IF EXISTS `lazada_order_items`;
CREATE TABLE `lazada_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL COMMENT '商户ID',
  `merchant_store_id` int(11) unsigned NOT NULL COMMENT '店铺id',
  `order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '订单id',
  `order_item_id` varchar(128) NOT NULL DEFAULT '' COMMENT '订单Item ID',
  `product_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Product ID',
  `sku_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Sku ID',
  `sku` varchar(128) NOT NULL DEFAULT '' COMMENT '商品SKU时间',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `item_price` varchar(20) NOT NULL DEFAULT '' COMMENT '商品价格',
  `status` varchar(20) NOT NULL DEFAULT '' COMMENT '状态',
  `pick_up_store_info` varchar(1000) NOT NULL DEFAULT '' COMMENT '提货商店信息',
  `purchase_order_number` varchar(128) NOT NULL DEFAULT '' COMMENT '调用SetPackedByMarketPlace时返回',
  `product_main_image` varchar(255) NOT NULL DEFAULT '' COMMENT '商品主图URL',
  `tax_amount` varchar(20) NOT NULL DEFAULT '' COMMENT '税额',
  `cancel_return_initiator` varchar(255) NOT NULL DEFAULT '' COMMENT '指示谁发起了取消或退回的订单。可能的值是取消-内部、取消-客户、取消-失败交付、取消-卖家、返回-客户和退款-内部。',
  `voucher_platform` varchar(255) NOT NULL DEFAULT '' COMMENT 'lazada发放的代金券',
  `voucher_seller` varchar(255) NOT NULL DEFAULT '' COMMENT '卖家出具的凭证',
  `order_type` varchar(20) NOT NULL DEFAULT '' COMMENT '订单类型 Normal, PreSale, Coupon, O2O , InStoreO2O',
  `stage_pay_status` varchar(20) NOT NULL DEFAULT '' COMMENT '预售订单在预售阶段的付款状态 null, unpaid ,unpaid final payment  （未付：未支付预售定金；未付尾款：已支付预售定金但未支付尾款/到期余额）',
  `warehouse_code` varchar(128) NOT NULL DEFAULT '' COMMENT '多卖家仓库代码',
  `voucher_seller_lpi` varchar(128) NOT NULL DEFAULT '' COMMENT '卖家赞助的lazada奖金',
  `voucher_platform_lpi` varchar(128) NOT NULL DEFAULT '' COMMENT 'lazada赞助的lazada奖金',
  `buyer_id` varchar(128) NOT NULL DEFAULT '' COMMENT '购买者ID',
  `shipping_fee_original` varchar(20) NOT NULL DEFAULT '' COMMENT '运费原件',
  `shipping_fee_discount_seller` varchar(20) NOT NULL DEFAULT '' COMMENT '卖家运费折扣',
  `shipping_fee_discount_platform` varchar(20) NOT NULL DEFAULT '' COMMENT '平台运费折扣',
  `voucher_code_seller` varchar(20) NOT NULL DEFAULT '' COMMENT '卖家提供的凭证代码',
  `voucher_code_platform` varchar(20) NOT NULL DEFAULT '' COMMENT '来自平台的凭证代码',
  `delivery_option_sof` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是卖家自有车队的标志，价值包括1和0。',
  `is_fbl` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'LAZADA是否满足的标记，值包括1和0。',
  `is_reroute` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为二次销售的标记，数值包括1和0。',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '取消、退货或表中定义的其他原因sales_order_reason',
  `digital_delivery_info` varchar(255) NOT NULL DEFAULT '' COMMENT '数字送货信息',
  `promised_shipping_time` varchar(20) NOT NULL DEFAULT '' COMMENT '承诺的发货时间',
  `voucher_amount` varchar(20) NOT NULL DEFAULT '' COMMENT '凭证金额',
  `return_status` varchar(20) NOT NULL DEFAULT '' COMMENT '返回状态',
  `shipping_type` varchar(20) NOT NULL DEFAULT '' COMMENT '运输类型，直接运输或仓库',
  `shipment_provider` varchar(128) NOT NULL DEFAULT '' COMMENT '3PL shipment provider, such as LEX',
  `variation` varchar(128) NOT NULL DEFAULT '' COMMENT '变异',
  `invoice_number` varchar(128) NOT NULL DEFAULT '' COMMENT '发票号码',
  `shipping_amount` varchar(20) NOT NULL DEFAULT '' COMMENT '运费',
  `currency` varchar(20) NOT NULL DEFAULT '' COMMENT '货币代码',
  `order_flag` varchar(30) NOT NULL DEFAULT '' COMMENT '订单的类型 GUARANTEE,NORMAL,GLOBAL_COLLECTION',
  `shop_id` varchar(128) NOT NULL DEFAULT '' COMMENT '卖家名称',
  `sla_time_stamp` varchar(20) NOT NULL DEFAULT '' COMMENT '船舶SLA时间',
  `voucher_code` varchar(128) NOT NULL DEFAULT '' COMMENT '未使用',
  `wallet_credits` varchar(128) NOT NULL DEFAULT '' COMMENT '钱包信用',
  `is_digital` varchar(10) NOT NULL DEFAULT '' COMMENT '是不是数字商品',
  `tracking_code_pre` varchar(128) NOT NULL DEFAULT '' COMMENT '未使用',
  `package_id` varchar(128) NOT NULL DEFAULT '' COMMENT '包裹ID',
  `tracking_code` varchar(128) NOT NULL DEFAULT '' COMMENT '从第三方物流运输供应商处检索跟踪代码',
  `shipping_service_cost` decimal(10,2) unsigned zerofill NOT NULL COMMENT '运输服务成本',
  `extra_attributes` varchar(1000) NOT NULL DEFAULT '' COMMENT '带有额外属性的JSON编码字符串',
  `paid_price` varchar(20) NOT NULL DEFAULT '' COMMENT '支付价格',
  `shipping_provider_type` varchar(20) NOT NULL DEFAULT '' COMMENT 'EXPRESS, STANDARD, ECONOMY, INSTANT, SELLER_OWN_FLEET, PICKUP_IN_STORE or DIGITAL',
  `product_detail_url` varchar(255) NOT NULL DEFAULT '' COMMENT '产品详细网址',
  `shop_sku` varchar(255) NOT NULL DEFAULT '' COMMENT '产品外部ID',
  `reason_detail` varchar(255) NOT NULL DEFAULT '' COMMENT '原因细节',
  `purchase_order_id` varchar(128) NOT NULL DEFAULT '' COMMENT '调用SetPackedByMarketPlace时返回',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '修改时间',
  `pull_created_at` datetime NOT NULL COMMENT 'API拉取首次入库时间',
  `pull_updated_at` datetime NOT NULL COMMENT 'API拉取入库更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for merchant
-- ----------------------------
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(20) NOT NULL DEFAULT '' COMMENT '商家类型 amazon,lazada,shopy',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未激活 1已激活 2已过期',
  `store_num_total` int(10) NOT NULL COMMENT '门店数量(总)',
  `store_num_used` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门店数量(已使用)',
  `expired_at` datetime NOT NULL COMMENT '有效期',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

-- ----------------------------
-- Table structure for merchant_store
-- ----------------------------
DROP TABLE IF EXISTS `merchant_store`;
CREATE TABLE `merchant_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家id',
  `platform` varchar(20) NOT NULL DEFAULT '' COMMENT '平台',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `unique_id` varchar(128) NOT NULL DEFAULT '' COMMENT '店铺唯一标识id',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态 0禁用 1启用 ',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='商户-店铺表';

-- ----------------------------
-- Table structure for merchant_users
-- ----------------------------
DROP TABLE IF EXISTS `merchant_users`;
CREATE TABLE `merchant_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL COMMENT '商戶id',
  `nickname` varchar(30) NOT NULL DEFAULT '' COMMENT '昵称',
  `account` varchar(30) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(128) NOT NULL DEFAULT '' COMMENT '密码',
  `store_ids` varchar(1000) NOT NULL DEFAULT '' COMMENT '可管理的店铺id集合',
  `role` varchar(255) NOT NULL DEFAULT '' COMMENT '角色 admin: 超级管理员',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0禁用 1启用',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `login_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
  `login_at` datetime DEFAULT NULL COMMENT '最后登录时间',
  `login_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='商户-店铺-用户表';

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(10) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL COMMENT '0未支付 1已支付 2已退款',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '顶级菜单id 默认0',
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `desc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(3) unsigned NOT NULL COMMENT '状态 0禁用 1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for shopee_config
-- ----------------------------
DROP TABLE IF EXISTS `shopee_config`;
CREATE TABLE `shopee_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) NOT NULL COMMENT '商户id',
  `merchant_store_id` int(11) unsigned NOT NULL COMMENT '店铺id',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '授权账户类型  shop/main   account',
  `app_key` int(10) unsigned NOT NULL,
  `app_secret` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0禁用 1启用',
  `created_at` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for shopee_order
-- ----------------------------
DROP TABLE IF EXISTS `shopee_order`;
CREATE TABLE `shopee_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(11) unsigned NOT NULL COMMENT '店铺id',
  `shop_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'shopee店铺id',
  `order_sn` varchar(128) NOT NULL DEFAULT '' COMMENT '订单编号',
  `total_amount` varchar(10) NOT NULL DEFAULT '' COMMENT '买家为订单支付的总金额。此金额包括商品的总销售价格、买家承担的运费；如果适用，并由Shopee促销活动抵消。该值仅在买家完成订单付款后返回。',
  `currency` varchar(10) NOT NULL DEFAULT '' COMMENT '货币',
  `actual_shipping_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单的实际运费，如果可以从外部物流合作伙伴处获得。',
  `actual_shipping_fee_confirmed` varchar(10) NOT NULL DEFAULT '' COMMENT '用这个存档来判断actual_shipping_fee是否被确认。',
  `buyer_cancel_reason` varchar(1000) NOT NULL DEFAULT '' COMMENT '买家取消原因，可能是空的。',
  `buyer_cpf_id` varchar(128) NOT NULL DEFAULT '' COMMENT '用于税务和发票目的的买方CPF号码。仅适用于巴西订单。',
  `buyer_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '此订单买家的用户id',
  `buyer_username` varchar(128) NOT NULL DEFAULT '' COMMENT '买家名称',
  `cancel_by` varchar(50) NOT NULL COMMENT '可以是买方、卖方、系统或操作系统之一。',
  `cancel_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '使用此字段获取买方、卖方和系统取消的原因。',
  `checkout_shipping_carrier` varchar(255) NOT NULL DEFAULT '' COMMENT '对于非掩蔽订单，买方为订单选择的物流服务提供商交付物品。对于掩蔽订单，买方为订单选择的物流服务类型交付物品。',
  `cod` varchar(10) NOT NULL COMMENT '此值指示订单是否为COD（货到付款）订单',
  `days_to_ship` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货准备时间由卖家在Shopee上列出商品时设置。',
  `dropshipper` varchar(255) NOT NULL DEFAULT '' COMMENT '仅适用于印度尼西亚订单。托运人的名称。',
  `dropshipper_phone` varchar(128) NOT NULL DEFAULT '' COMMENT '收件人的电话号码,可能为空。',
  `edt_from` varchar(255) NOT NULL DEFAULT '' COMMENT '订单最早预计交货日期（仅适用于BR地区）',
  `edt_to` varchar(255) NOT NULL DEFAULT '' COMMENT '订单的最新预计交货日期（仅适用于BR地区）',
  `estimated_shipping_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预计运费是Shopee根据特定物流快递标准计算的估计。',
  `fulfillment_flag` varchar(128) NOT NULL DEFAULT '' COMMENT '使用此字段指示订单由购物者或卖家完成。适用值：fulfilled_by_shopee、fulfilled_by_cb_seller、fulfilled_by_local_seller。',
  `goods_to_declare` varchar(10) NOT NULL DEFAULT '' COMMENT '仅适用于跨境订单。此值指示订单是否包含需要在海关申报的货物。“T”表示真，它将在运输标签上标记为“T”；“F”表示假，它将在运输标签上标记为“P”。此值仅在订单跟踪号生成后才准确，请在检索跟踪号后捕获此值。',
  `invoice_data` varchar(500) NOT NULL DEFAULT '' COMMENT '订单的发票数据。en：订单的电子发票（NF-e）。',
  `message_to_seller` varchar(255) NOT NULL DEFAULT '' COMMENT '给卖家的消息',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '纸币销售商为自己做了参考',
  `note_update_time` int(10) unsigned NOT NULL COMMENT 'Update time for the note.',
  `order_chargeable_weight_gram` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对于CB店，显示重量用于计算此订单的actual_shipping_fee',
  `order_status` varchar(30) NOT NULL DEFAULT '' COMMENT '订单状态',
  `payment_method` varchar(255) NOT NULL DEFAULT '' COMMENT '买家选择支付订单的支付方式。',
  `pickup_done_time` int(10) unsigned NOT NULL COMMENT '取件完成时的时间戳',
  `prescription_check_status` int(10) unsigned DEFAULT '0' COMMENT '返回此订单枚举的处方检查状态OrderPres脚本检查状态：NONE=0；通过=1；失败=2；仅适用于ID白名单用户。',
  `prescription_images` varchar(255) NOT NULL DEFAULT '' COMMENT '返回此订单所有处方图片的列表，仅适用于ID白名单用户。',
  `recipient_address` varchar(500) NOT NULL DEFAULT '' COMMENT '此对象包含收件人地址的详细细分。',
  `region` varchar(10) NOT NULL DEFAULT '' COMMENT '国家二字码',
  `reverse_shipping_fee` varchar(10) NOT NULL DEFAULT '' COMMENT 'Shopee收取退回订单的反向运费。',
  `ship_by_date` int(10) unsigned DEFAULT NULL COMMENT '包裹发出的期限',
  `shipping_carrier` varchar(128) NOT NULL DEFAULT '' COMMENT '买方为订单选择的交付物品的物流服务提供商。',
  `split_up` varchar(10) NOT NULL DEFAULT '' COMMENT '指示此订单是否拆分为完整订单（Forder）级别。',
  `pay_time` int(10) unsigned DEFAULT NULL COMMENT '订单状态从UNPAID更新为PAID的时间。当订单尚未付款时，此值为NULL。',
  `create_time` int(11) unsigned NOT NULL COMMENT '指示创建订单的日期和时间的时间戳。',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '订单值上次更改的时间',
  `package_list` text NOT NULL COMMENT '订单下的包裹列表',
  `created_at` datetime NOT NULL COMMENT 'API 首次拉取时间',
  `updated_at` datetime NOT NULL COMMENT 'API最后一次拉取时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for shopee_order_items
-- ----------------------------
DROP TABLE IF EXISTS `shopee_order_items`;
CREATE TABLE `shopee_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL COMMENT '商户id',
  `merchant_store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `shop_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'shopee店铺id',
  `order_sn` varchar(128) NOT NULL DEFAULT '' COMMENT '订单编号',
  `item_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Shopee订单项的唯一标识符',
  `item_name` varchar(500) NOT NULL COMMENT '订单项名称',
  `item_sku` varchar(255) NOT NULL COMMENT '项目SKU（库存单位）是卖家定义的标识符，有时称为父SKU。项目SKU可以分配给Shopee列表中的项目',
  `model_id` varchar(128) NOT NULL DEFAULT '' COMMENT '属于同一项目的型号的ID',
  `model_name` varchar(255) NOT NULL DEFAULT '' COMMENT '属于同一商品的型号名称。卖家可以提供同一商品的型号',
  `model_sku` varchar(128) NOT NULL COMMENT '模型SKU（库存单位）是卖家定义的标识符。它仅供卖家使用',
  `model_quantity_purchased` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '同一买家同时从一个列表/项目中购买的相同物品的数量',
  `model_original_price` int(10) unsigned NOT NULL COMMENT '以挂牌货币计算的项目原价。',
  `model_discounted_price` int(10) unsigned NOT NULL COMMENT '以上市货币计算的项目折扣后价格',
  `wholesale` varchar(10) NOT NULL DEFAULT '' COMMENT '此值指示买方是否以批发价购买订单项目',
  `weight` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '物品的重量',
  `add_on_deal` varchar(128) NOT NULL DEFAULT '' COMMENT '指示此项目是否属于插件交易。',
  `main_item` varchar(128) NOT NULL DEFAULT '' COMMENT '指示此项目是主项目还是子项目。真表示主项目，假表示子项目',
  `add_on_deal_id` varchar(128) NOT NULL DEFAULT '' COMMENT '用于区分购物车和订单中的项目组的唯一ID',
  `promotion_type` varchar(128) NOT NULL DEFAULT '' COMMENT '可用类型：product_promotion，flash_sale，group_by，bundle_deal，add_on_deal_main，add_on_deal_sub，add_on_free_gift_main，add_on_free_gift_sub',
  `promotion_id` varchar(128) NOT NULL DEFAULT '' COMMENT '促销的ID。',
  `order_item_id` varchar(128) NOT NULL DEFAULT '' COMMENT '订单项目的标识',
  `promotion_group_id` varchar(128) NOT NULL DEFAULT '' COMMENT '产品推广的识别。',
  `image_info` text NOT NULL COMMENT '产品的图片信息。',
  `product_location_id` varchar(255) NOT NULL DEFAULT '' COMMENT '项目的仓库ID列表。',
  `is_prescription_item` varchar(10) NOT NULL DEFAULT '' COMMENT '对于顺序中的每个项目，返回该项目是否为处方项目。',
  `is_b2c_owned_item` varchar(10) NOT NULL DEFAULT '' COMMENT '确定项目是否B2C_shop_item。',
  `created_at` datetime NOT NULL COMMENT 'API首次拉取时间',
  `updated_at` datetime NOT NULL COMMENT 'API最后一次更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for shopee_shop
-- ----------------------------
DROP TABLE IF EXISTS `shopee_shop`;
CREATE TABLE `shopee_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺名',
  `region` varchar(5) NOT NULL DEFAULT '' COMMENT '国家/地区',
  `status` varchar(20) NOT NULL COMMENT '状态',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for system_auth
-- ----------------------------
DROP TABLE IF EXISTS `system_auth`;
CREATE TABLE `system_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限名称',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限描述',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` smallint(5) unsigned NOT NULL COMMENT '状态 0禁用 1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for system_menu
-- ----------------------------
DROP TABLE IF EXISTS `system_menu`;
CREATE TABLE `system_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '菜单链接',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图标',
  `params` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接参数',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for system_users
-- ----------------------------
DROP TABLE IF EXISTS `system_users`;
CREATE TABLE `system_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '头像',
  `gender` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '性别 0女 1男',
  `mobile` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `remark` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
