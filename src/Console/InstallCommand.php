<?php

namespace LiveBlade\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'liveblade:install {--force : Overwrite any existing files}';
    protected $description = 'Install LiveBlade package assets and configuration';

    public function handle()
    {
        $this->info('🚀 Installing LiveBlade...');
        
        // Publish assets (JS, CSS files)
        $this->call('vendor:publish', [
            '--provider' => 'LiveBlade\\LiveBladeServiceProvider',
            '--tag' => 'liveblade-assets',
            '--force' => $this->option('force'),
        ]);
        
        // Publish configuration file
        $this->call('vendor:publish', [
            '--provider' => 'LiveBlade\\LiveBladeServiceProvider',
            '--tag' => 'liveblade-config',
            '--force' => $this->option('force'),
        ]);
        
        // Publish views (optional)
        if ($this->confirm('Do you want to publish the LiveBlade views?', false)) {
            $this->call('vendor:publish', [
                '--provider' => 'LiveBlade\\LiveBladeServiceProvider',
                '--tag' => 'liveblade-views',
                '--force' => $this->option('force'),
            ]);
        }
        
        // Create a liveblade layout stub (optional)
        $this->createLayoutStub();
        
        $this->info('✅ LiveBlade installed successfully!');
        $this->newLine();
        $this->line('📝 Next steps:');
        $this->line('   1. Add @livebladeStyles() to your layout <head>');
        $this->line('   2. Add @livebladeScripts() before closing </body>');
        $this->line('   3. Run npm install && npm run build (if using Vite)');
        $this->line('   4. Start using LiveBlade components in your Blade templates');
        $this->newLine();
        $this->line('🎉 For documentation, visit: https://github.com/Eluk-Samuel-Kiira/liveblade');
    }
    
    /**
     * Create a layout stub file with LiveBlade directives
     */
    protected function createLayoutStub()
    {
        $layoutPath = resource_path('views/layouts/app.blade.php');
        
        if (!file_exists($layoutPath)) {
            $stubPath = __DIR__.'/../../stubs/layout.stub';
            
            if (!is_dir(dirname($layoutPath))) {
                mkdir(dirname($layoutPath), 0755, true);
            }
            
            if (file_exists($stubPath)) {
                copy($stubPath, $layoutPath);
                $this->info('📄 Created layout stub at: resources/views/layouts/app.blade.php');
            }
        }
    }
}