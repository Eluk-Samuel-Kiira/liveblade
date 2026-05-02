<?php

namespace LiveBlade;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use LiveBlade\Console\InstallCommand;

class LiveBladeServiceProvider extends ServiceProvider
{
    public function boot()
    {

        Blade::component('liveblade::components.search', 'liveblade-search');
        Blade::component('liveblade::components.pagination', 'liveblade-pagination');
        Blade::component('liveblade::components.loader', 'liveblade-loader');

        ], 'liveblade-assets');

        ], 'liveblade-config');

                InstallCommand::class,
            ]);
        }
    }

    public function register()
    {
    }
}
