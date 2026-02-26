<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Policies\ClientPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\QuotePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Quote::class, QuotePolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
    }
}