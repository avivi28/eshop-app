<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if (!in_array(request()->ip(), $whitelist)) {
            // URL::forceScheme('https');
        }

        Response::macro('error', function ($error, $httpStatusCode = 400) {

            $version = 'v1';

            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $output->writeln($httpStatusCode);

            $customFormat = [
                "server_time" => time(),
                'version' => $version,
                'success' => false,
                'message' => $error,
                'data' => null
            ];

            return Response::make($customFormat, $httpStatusCode);
        });

        Response::macro('api', function ($data, $success = true, $error = null) {

            $version = 'v1';

            if (!is_array($data) && method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }

            if (is_array($data) && array_key_exists('data', $data) == true) {

                $customFormat = [
                    "server_time" => time(),
                    'version' => $version,
                    'success' => $success,
                    'message' => $error
                ];

                $customFormat = array_merge($customFormat, $data);
            } else {

                $customFormat = [
                    "server_time" => time(),
                    'version' => $version,
                    'success' => $success,
                    'message' => $error,
                    'data' => $data
                ];
            }

            return Response::make($customFormat);
        });
    }
}
