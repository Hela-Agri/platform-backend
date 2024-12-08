<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();
        DB::connection()
        ->getDoctrineSchemaManager()
        ->getDatabasePlatform()
        ->registerDoctrineTypeMapping('enum', 'string');
    }
}
