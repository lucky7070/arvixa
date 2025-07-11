<?php

namespace App\Providers;

use App\Models\Ledger;
use Maatwebsite\Excel\Sheet;
use Illuminate\Pagination\Paginator;
use App\Models\Observer\LedgerObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
    }
}
