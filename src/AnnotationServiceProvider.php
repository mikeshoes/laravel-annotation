<?php
namespace LaravelAnnotation;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use LaravelAnnotation\Commands\AnnotationCache;
use LaravelAnnotation\Commands\AnnotationClear;
use Symfony\Component\HttpKernel\Kernel;

class AnnotationServiceProvider extends ServiceProvider implements DeferrableProvider
{

    public function boot()
    {
        $source = realpath(__DIR__ . '/config/annotation.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('annotation.php')]);
        }

        $this->mergeConfigFrom($source, 'annotation');
    }


    public function register()
    {
        // 注册命令
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->commands([AnnotationClear::class, AnnotationCache::class]);
            return;
        }
    }

}