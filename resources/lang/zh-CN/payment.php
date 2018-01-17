<?php
return [
    'index' => '付款管理',

    'schedules' => '付款计划',
    'schedule'  => '付款计划明细',
    'schedule.name' => '编号',
    'schedule.supplier_name' => '供应商名称(导入)',
    'schedule.supplier' => '供应商(匹配)',
    'schedule.supplier_balance' => '供应商余款(未付款总额)',
    'schedule.bill_period' => '账期',
    'schedule.due_money'   => '应付款',
    'schedule.paid_money'   => '已付款',
    'schedule.cash_paid'   => '已付现金',
    'schedule.acceptance_paid'   => '已付承兑',
    'schedule.plan_time'   => '计划时间',
    'schedule.batch'   => '导入批次',
    'schedule.user'   => '操作人',

    'schedule.status'  => '状态',
    'schedule.status.init'  => '初始化',
    'schedule.status.import_init'  => '初始化(导入)',
    'schedule.status.web_init'     => '初始化(web)',
    'schedule.status.checked'      => '已审核',
    'schedule.status.paying'       => '付款中',
    'schedule.status.lock'         => '锁定',


    'schedule.is_checked.true'       => '已审核',
    'schedule.is_checked.false'       => '未审核',
    'schedule.is_locked.true'         => '已锁定',
    'schedule.is_locked.false'         => '未锁定',


    'details'       => '付款',
    'detail'        => '付款明细',
    'detail.time'   => '付款时间',
    'detail.money'  => '付款金额',

    'detail.pay_type'            => '付款方式',
    'detail.pay_type.cash'       => '现金',
    'detail.pay_type.acceptance' =>'承兑',

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