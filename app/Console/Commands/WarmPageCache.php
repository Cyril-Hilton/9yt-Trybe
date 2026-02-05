<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class WarmPageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-cache:warm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm the page cache by visiting critical public routes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = config('app.url');
        $this->info("Warming cache for base URL: {$baseUrl}");

        $routes = [
            '/',
            '/news',
            '/today',
            '/this-weekend',
            // Top regions
            '/locations/Greater Accra',
            '/locations/Ashanti',
        ];

        foreach ($routes as $route) {
            $url = $baseUrl . $route;
            $this->info("Visiting: {$url}");
            
            try {
                // We use a timeout to prevent hanging, but we don't need the body
                // Just the act of requesting triggers the middleware
                Http::timeout(10)->get($url);
                $this->info("✔ Warm");
            } catch (\Exception $e) {
                $this->error("✘ Failed: " . $e->getMessage());
            }
        }

        $this->info('Cache warming completed.');
        return Command::SUCCESS;
    }
}
