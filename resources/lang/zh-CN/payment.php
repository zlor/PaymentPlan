<?php
return [
    'index' => '付款管理',

    'period.pay' => '按账期付款',

    'types' => '类型',
    'type'  => '类型明细',
    'type.name' => '类型名称',
    'type.code' => '类型编号',
    'type.icon' => '类型标识',
    'type.map_sheet'  => '是否映射Sheet',
    'type.map_sheet.bool.1'  => '是',
    'type.map_sheet.bool.0'  => '否',
    'type.sheet_slug' => '映射标记',

    'materiels' => '物料',
    'materiel'  => '物料明细',
    'materiel.name' => '物料名称',
    'materiel.code' => '物料编号',
    'materiel.icon' => '物料标识',

    'schedules' => '付款计划',
    'schedule'  => '付款计划明细',
    'schedule.importInfo' => '导入信息',
    'schedule.planInfo'   => '计划信息',
    'schedule.auditInfo'  => '一次核定',
    'schedule.finalInfo'  => '二次核定',
    'schedule.lockInfo'  => '最终敲定',
    'schedule.payInfo'    => '付款信息',
    'schedule.supplierBalanceInfo' => '应付款信息',

    'schedule.name' => '科目编号',
    'schedule.supplier_name' => '供应商名称',
    'schedule.supplier' => '供应商',
    'schedule.supplier_balance' => '总应付款',
    'schedule.supplier_lpu_balance'   => '上期未付清余额',//supplier_last_period_unpaid_balance

    'schedule.pay_cycle'    => '付款周期',
    'schedule.pay_cycle_month' =>'应付到期月份',
    'schedule.charge_man'    => '付款确认人',

    'schedule.materiel_name' => '物料名称(导入)',
    'schedule.payment_materiel' => '物料',

    'schedule.payment_type' => '类型', // 物料类型

    'schedule.bill_period' => '账期',
    'schedule.suggest_due_money' => '建议应付款',

    'schedule.plan_time' => '计划时间', // 导入时间
    'schedule.plan_due_money' => '计划应付款',
    'schedule.plan_man' => '计划人',

    'schedule.audit_time' => '审核时间',
    'schedule.audit_due_money' => '应付款(一次核定)',
    'schedule.audit_man' => '审核人',

    'schedule.final_time' => '终稿时间',
    'schedule.final_due_money' => '应付款(二次核定)',
    'schedule.final_man' => '终稿人',

    'schedule.invoice_m_1' => '1月',
    'schedule.invoice_m_2' => '2月',
    'schedule.invoice_m_3' => '3月',
    'schedule.invoice_m_4' => '4月',
    'schedule.invoice_m_5' => '5月',
    'schedule.invoice_m_6' => '6月',
    'schedule.invoice_m_7' => '7月',
    'schedule.invoice_m_8' => '8月',
    'schedule.invoice_m_9' => '9月',
    'schedule.invoice_m_10' => '10月',
    'schedule.invoice_m_11' => '11月',
    'schedule.invoice_m_12' => '12月',


    'schedule.due_money'   => '应付款(最终敲定)',
    'schedule.paid_money'   => '已付款',
    'schedule.cash_paid'   => '已付现金',
    'schedule.acceptance_paid'   => '已付承兑',
    'schedule.batch'   => '导入批次',
    'schedule.user'   => '操作人',

    'schedule.status'  => '状态',
    'schedule.status.init'  => '初始化',
    'schedule.status.import_init'  => '初始化(导入)',
    'schedule.status.web_init'     => '初始化(web)',
    'schedule.status.check_audit'      => '一次核定',
    'schedule.status.check_final'      => '二次核定',
    'schedule.status.checked'      => '已核定',

    'schedule.status.plan'      => '计划中',
    'schedule.status.paying'       => '付款中',
    'schedule.status.lock'         => '锁定',


    'schedule.is_checked.true'       => '已审核',
    'schedule.is_checked.false'       => '未审核',
    'schedule.is_locked.true'         => '已锁定',
    'schedule.is_locked.false'         => '未锁定',

    'schedule.is_froze'   => '是否暂停付款',
    'schedule.is_froze.true'   => '暂停付款',
    'schedule.is_froze.false'  => '允许付款',


    'details'       => '付款',
    'detail'        => '付款明细',
    'detail.time'   => '付款时间',
    'detail.money'  => '付款金额',

    'detail.pay_type'            => '付款方式',
    'detail.pay_type.cash'       => '现金',
    'detail.pay_type.acceptance' => '承兑',
    'detail.pay_type.tele_transfer' => '电汇',

    'detail.code'   => '付款流水号',
    'detail.payment_proof' => '付款凭证',
    'detail.collecting_company' => '收款公司',
    'detail.collecting_proof' => '收款凭证',

    'detail.memo' =>'备注',

    'detail.bill_period' => '账期',
    'detail.supplier'    => '供应商',
    'detail.payment_schedule' => '付款计划',
    'detail.user' => '操作人',
];