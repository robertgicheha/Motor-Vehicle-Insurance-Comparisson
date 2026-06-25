<?php

namespace App\Providers;

use App\Models\Quotation;
use App\Policies\QuotationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Quotation::class => QuotationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
