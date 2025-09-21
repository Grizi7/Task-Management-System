<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $models = getFilesFromPath('App/Models');
        foreach ($models as $model) {
            $modelClass = "App\\Models\\{$model}";
            $repoClass = "App\\Repositories\\{$model}Repository";
            $repoInterface = "App\\Contracts\\{$model}Contract";
            $serviceClass = "App\\Services\\{$model}Service";
            $serviceInterface = "App\\Core\\Contracts\\ServiceInterface";

            if (class_exists($repoClass) && class_exists($modelClass) && interface_exists($repoInterface)) {
                $this->app->bind(
                    $repoInterface,
                    function ($app) use ($repoClass, $modelClass) {
                        return new $repoClass(new $modelClass);
                    }
                );
            }

            if (class_exists($serviceClass) && interface_exists($serviceInterface) && interface_exists($repoInterface)) {
                $this->app->bind(
                    $serviceInterface,
                    function ($app) use ($serviceClass, $repoInterface) {
                        return new $serviceClass($app->make($repoInterface));
                    }
                );
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
