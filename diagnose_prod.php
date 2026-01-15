<?php
/**
 * 9yt !Trybe Production Diagnostic Script
 * Purpose: Pinpoint why the homepage might be returning a 500 error.
 * Run via: php diagnose_prod.php
 */

define('LARAVEL_START', microtime(true));

// --- Phase 0: Path Detection ---
echo "--- Phase 0: Path Detection ---\n";
$possible_paths = [
    __DIR__ . '/vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];

$autoload_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $autoload_path = $path;
        break;
    }
}

if (!$autoload_path) {
    echo "ERROR: Could not find vendor/autoload.php in any of these locations:\n";
    foreach ($possible_paths as $path) echo " - $path\n";
    echo "\nCurrent Directory: " . __DIR__ . "\n";
    echo "Files in current directory:\n";
    print_r(scandir(__DIR__));
    exit(1);
}

echo "Found autoload at: $autoload_path\n";
require $autoload_path;

$base_dir = dirname($autoload_path);
$app_path = $base_dir . '/bootstrap/app.php';

if (!file_exists($app_path)) {
    echo "ERROR: Found vendor but could not find bootstrap/app.php at $app_path\n";
    exit(1);
}

try {
    echo "--- Phase 1: Bootstrapping ---\n";
    $app = require_once $app_path;
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "SUCCESS: Laravel bootstrapped successfully.\n\n";

    echo "--- Phase 2: Configuration Check ---\n";
    echo "App Env: " . config('app.env') . "\n";
    echo "App Debug: " . (config('app.debug') ? 'TRUE' : 'FALSE') . "\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "SUCCESS: Configuration loaded.\n\n";

    echo "--- Phase 3: Blade Compilation Check ---\n";
    $welcomePath = base_path('resources/views/welcome.blade.php');
    if (!file_exists($welcomePath)) {
        throw new Exception("welcome.blade.php NOT FOUND at $welcomePath");
    }
    echo "Compiling welcome.blade.php...\n";
    $compiled = Illuminate\Support\Facades\Blade::compileString(file_get_contents($welcomePath));
    echo "SUCCESS: Blade compilation successful.\n\n";

    echo "--- Phase 4: Controller Execution ---\n";
    $request = Illuminate\Http\Request::create('/', 'GET');
    $controller = $app->make(App\Http\Controllers\Public\EventController::class);
    $newsService = $app->make(App\Services\News\NewsService::class);
    
    echo "Running EventController@home...\n";
    $response = $controller->home($request, $newsService);
    
    if ($response instanceof \Illuminate\View\View) {
        echo "SUCCESS: Controller returned a View. Attempting to render...\n";
        $html = $response->render();
        echo "SUCCESS: View rendered successfully (Length: " . strlen($html) . " chars).\n";
    } elseif ($response instanceof \Illuminate\Http\Response) {
        echo "SUCCESS: Controller returned a Response. Status: " . $response->getStatusCode() . "\n";
        $html = $response->getContent();
        echo "SUCCESS: Content Length: " . strlen($html) . " chars.\n";
    } else {
        echo "Controller returned: " . get_class($response) . "\n";
    }

    echo "--- DIAGNOSTIC COMPLETE: NO ERRORS DETECTED ---\n";
    echo "If you still see a 500 error in the browser, it might be a server-level issue (e.g., .htaccess or nginx config).\n";

} catch (\Throwable $e) {
    echo "\n!!! ERROR DETECTED !!!\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
