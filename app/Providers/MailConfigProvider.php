<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\MailConfig;

class MailConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $configuration = MailConfig::first();

            if (!is_null($configuration)) {
                $config = [
                    'driver' => $configuration->driver,
                    'host' => $configuration->host,
                    'port' => $configuration->port,
                    'username' => $configuration->username,
                    'password' => $configuration->password,
                    'encryption' => $configuration->encryption,
                    'from' => ['address' => $configuration->email_id, 'name' => $configuration->mailerName],
                ];
                Config::set('mail', $config);
            }
        } catch (\Exception $e) {
            // Handle the exception gracefully, perhaps by logging it
            // For now, we'll simply log the error message
            \Log::error('Error fetching mail configuration: ' . $e->getMessage());
        }
    }
}
