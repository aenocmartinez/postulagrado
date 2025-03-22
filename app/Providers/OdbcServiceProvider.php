<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\Connector;

class OdbcServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('db.connector.odbc', function () {
            return new class extends Connector implements ConnectorInterface {
                public function connect(array $config)
                {
                    $dsn = $config['dsn'];
                    $username = $config['username'] ?? null;
                    $password = $config['password'] ?? null;

                    return $this->createConnection("odbc:{$dsn}", $config, $username, $password);
                }
            };
        });

        $this->app->bind('db.connection.odbc', function ($app, $parameters) {
            [$pdo, $database, $prefix, $config] = $parameters;

            return new Connection($pdo, $database, $prefix, $config);
        });
    }

    public function boot()
    {
        // Requerido en Laravel 11 para asegurar el ciclo de carga
    }
}
