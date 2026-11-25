<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Shared-hosting layout: the Laravel app lives in app-core/ beside this file.
if (file_exists($maintenance = __DIR__.'/app-core/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/app-core/vendor/autoload.php';

(require_once __DIR__.'/app-core/bootstrap/app.php')
    ->handleRequest(Request::capture());
