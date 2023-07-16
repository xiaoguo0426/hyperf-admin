<?php
return [
    //即时报告
    'requested' => [
        'GET_RESTOCK_INVENTORY_RECOMMENDATIONS_REPORT',//补货库存报告
        'GET_AFN_INVENTORY_DATA',//FBA亚马逊完成库存报告
        'GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA',//FBA管理库存
        'GET_FBA_INVENTORY_PLANNING_DATA',//FBA管理库存健康报告
        'GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA',//FBA预估费用报告
        'GET_SALES_AND_TRAFFIC_REPORT',//销售与流量业务报告
        'GET_SALES_AND_TRAFFIC_REPORT_CUSTOM',//销售与流量业务报告(指定日期范围，用于统计最近3天，7天，14天，30天销量)
        'GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA',//FBA退货报告
        'GET_FBA_REIMBURSEMENTS_DATA',//FBA赔偿报告
        'GET_FBA_FULFILLMENT_REMOVAL_ORDER_DETAIL_DATA',//FBA受损货物明细报告
        'GET_SELLER_FEEDBACK_DATA',//评估卖方表现的买家的负面和中性反馈（一到三颗星）报告
//        'GET_V2_SELLER_PERFORMANCE_REPORT',//店铺绩效 详见 https://blog.csdn.net/qq594865227/article/details/123263007?spm=1001.2101.3001.6650.5&utm_medium=distribute.pc_relevant.none-task-blog-2%7Edefault%7EBlogCommendFromBaidu%7ERate-5-123263007-blog-117392280.235%5Ev31%5Epc_relevant_default_base3&depth_1-utm_source=distribute.pc_relevant.none-task-blog-2%7Edefault%7EBlogCommendFromBaidu%7ERate-5-123263007-blog-117392280.235%5Ev31%5Epc_relevant_default_base3&utm_relevant_index=6
//        'GET_COUPON_PERFORMANCE_REPORT'
    ],
    //周期报告
    'scheduled' => [
        'GET_V2_SETTLEMENT_REPORT_DATA_FLAT_FILE_V2',//付款报告
        'GET_DATE_RANGE_FINANCIAL_TRANSACTION_DATA',//日期范围报告
    ]
];