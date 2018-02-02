<?php

namespace App\Providers;

use App\Models\BillCollect;
use App\Models\BillPay;
use App\Models\BillPeriodFlow;
use App\Models\PaymentDetail;
use App\Observers\BillCollectObserver;
use App\Observers\BillPayObserver;
use App\Observers\BillPeriodFlowObserver;
use App\Observers\PaymentDetailObserver;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // utf8mb64 时控制字符长度
        Schema::defaultStringLength(191);
        //
        Config::load();

        // 计划明细 -（绑定生成相应的付款记录）
        PaymentDetail::observe(PaymentDetailObserver::class);

        // 付款明细 - (触发：账单流水-BillFlows)
        BillPay::observe(BillPayObserver::class);
        // 收款明细 - (触发：账单流水-BillFlows)
        BillCollect::observe(BillCollectObserver::class);

        // 账单流水 - (触发：账期汇总信息)
        BillPeriodFlow::observe(BillPeriodFlowObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        if ($this->app->environment() !== 'production')
        {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
