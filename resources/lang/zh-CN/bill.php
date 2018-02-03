<?php
return [
    'periods' => '账期',

    'period'  => '',
    'period.name'  => '名称',
    'period.month'  => '年月',
    'period.charge_man'  => '负责人',

    'period.status'  => '状态',
    'period.status.standby'  => '就绪',
    'period.status.active'   => '激活',
    'period.status.lock'   => '锁定',
    'period.status.close'   => '关闭',


    'period.time'  => '账期范围',
    'period.balance_pool'    => '资金池',
    'period.cash_pool'       => '现金池',  // 银行存款 + 确认应收款
    'period.load_pool'       => '预支额',  // 承兑额度 + 银行贷款

    'period.cash_balance'    => '银行存款',
    'period.invoice_balance' => '确认应收款',
    'period.acceptance_line' => '承兑额度',
    'period.loan_balance'    => '银行贷款',

    'period.except_balance'  => '预计收款总额',

    'period.cash_paid'       => '已支付现金',
    'period.acceptance_paid' => '已支付承兑',
    'period.cash_collected'       => '已收回现金',
    'period.acceptance_collected' => '已收回承兑',

    'period.user'  => '操作人',
    'period.balance' => '账期余额', // 银行存款 + 承兑额度 + 银行贷款 + 确认应收款(未关联实际收款) - 支付现金 - 支付承兑


];