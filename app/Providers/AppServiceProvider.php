<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        DB::extend('odbc', function ($config, $name) {
            $dsn = "odbc:{$config['dsn']}";
            $username = $config['username'] ?? null;
            $password = $config['password'] ?? null;

            $pdo = new \PDO($dsn, $username, $password);

            return new Connection($pdo, $config['database'], $config['prefix'] ?? '', $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
