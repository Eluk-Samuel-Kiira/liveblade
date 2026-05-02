<?php

namespace LiveBlade;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use LiveBlade\Console\InstallCommand;

class LiveBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'liveblade');
        
        // Register Blade components
        Blade::component('liveblade::components.search', 'liveblade-search');
        Blade::component('liveblade::components.pagination', 'liveblade-pagination');
        Blade::component('liveblade::components.loader', 'liveblade-loader');
        
        // Publish assets (JS, CSS files)
        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/liveblade'),
        ], 'liveblade-assets');
        
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/liveblade.php' => config_path('liveblade.php'),
        ], 'liveblade-config');
        
        // Publish views (optional)
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/liveblade'),
        ], 'liveblade-views');
        
        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
        
        // Register Blade directives
        $this->registerBladeDirectives();
    }
    
    /**
     * Register custom Blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        // Scripts directive
        Blade::directive('livebladeScripts', function () {
            return "<?php echo view('liveblade::scripts')->render(); ?>";
        });
        
        // Styles directive
        Blade::directive('livebladeStyles', function () {
            return "<?php echo view('liveblade::styles')->render(); ?>";
        });
    }
    
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/liveblade.php', 'liveblade'
        );
        
        // Register the main LiveBlade class
        $this->app->singleton('liveblade', function ($app) {
            return new \LiveBlade\LiveBlade();
        });
    }

    // @php
    // $assetUrl = config('liveblade.asset_url', '/vendor/liveblade');
    // @endphp

    // <script src="{{ $assetUrl }}/liveblade.min.js"></script>
    // <script>
    //     // Auto-initialize LiveBlade when DOM is ready
    //     document.addEventListener('DOMContentLoaded', function() {
    //         if (typeof LiveBlade !== 'undefined') {
    //             LiveBlade.init();
    //         }
    //     });
    // </script>

    // @php
    // $assetUrl = config('liveblade.asset_url', '/vendor/liveblade');
    // @endphp

    // <link rel="stylesheet" href="{{ $assetUrl }}/liveblade.css">

}