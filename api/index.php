<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$setDefaultEnvironment = static function (string $key, string $value): void {
    if (getenv($key) !== false || isset($_ENV[$key]) || isset($_SERVER[$key])) {
        return;
    }

    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
};

$forceEnvironment = static function (string $key, string $value): void {
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
};

$temporaryStorage = '/tmp/oceanpaws-storage';

foreach ([
    '/tmp/oceanpaws-cache',
    $temporaryStorage.'/app/private',
    $temporaryStorage.'/app/public',
    $temporaryStorage.'/framework/cache/data',
    $temporaryStorage.'/framework/sessions',
    $temporaryStorage.'/framework/views',
    $temporaryStorage.'/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

$setDefaultEnvironment('APP_ENV', 'production');
$setDefaultEnvironment('APP_DEBUG', 'false');
$setDefaultEnvironment('LOG_CHANNEL', 'stderr');
$setDefaultEnvironment('LOG_STACK', 'stderr');
$setDefaultEnvironment('CACHE_STORE', 'array');
$setDefaultEnvironment('SESSION_DRIVER', 'cookie');
$setDefaultEnvironment('SESSION_ENCRYPT', 'true');
$setDefaultEnvironment('SESSION_SECURE_COOKIE', 'true');
$setDefaultEnvironment('QUEUE_CONNECTION', 'sync');
$setDefaultEnvironment('FILESYSTEM_DISK', 'local');
$setDefaultEnvironment('LARAVEL_STORAGE_PATH', $temporaryStorage);
$setDefaultEnvironment('VIEW_COMPILED_PATH', $temporaryStorage.'/framework/views');
$setDefaultEnvironment('APP_CONFIG_CACHE', '/tmp/oceanpaws-cache/config.php');
$setDefaultEnvironment('APP_EVENTS_CACHE', '/tmp/oceanpaws-cache/events.php');
$setDefaultEnvironment('APP_PACKAGES_CACHE', '/tmp/oceanpaws-cache/packages.php');
$setDefaultEnvironment('APP_ROUTES_CACHE', '/tmp/oceanpaws-cache/routes.php');
$setDefaultEnvironment('APP_SERVICES_CACHE', '/tmp/oceanpaws-cache/services.php');

// Values copied from a local .env must not make a Vercel function depend on
// local database-backed sessions, cache, queues, or expose debug traces.
if (getenv('VERCEL') !== false) {
    foreach ([
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'LOG_CHANNEL' => 'stderr',
        'LOG_STACK' => 'stderr',
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'cookie',
        'SESSION_ENCRYPT' => 'true',
        'SESSION_SECURE_COOKIE' => 'true',
        'QUEUE_CONNECTION' => 'sync',
        'FILESYSTEM_DISK' => 'local',
    ] as $key => $value) {
        $forceEnvironment($key, $value);
    }
}

if (getenv('APP_URL') === false) {
    $vercelHost = getenv('VERCEL_PROJECT_PRODUCTION_URL') ?: getenv('VERCEL_URL');

    if ($vercelHost) {
        $setDefaultEnvironment('APP_URL', 'https://'.$vercelHost);
    }
}

if (getenv('DB_URL') === false) {
    $databaseUrl = getenv('DATABASE_URL')
        ?: getenv('MYSQL_PUBLIC_URL')
        ?: getenv('MYSQL_URL')
        ?: getenv('POSTGRES_URL')
        ?: getenv('POSTGRES_PRISMA_URL');

    if ($databaseUrl) {
        $setDefaultEnvironment('DB_URL', $databaseUrl);
        $setDefaultEnvironment(
            'DB_CONNECTION',
            str_starts_with($databaseUrl, 'mysql') ? 'mysql' : 'pgsql',
        );
    }
}

if (getenv('APP_KEY') === false || trim((string) getenv('APP_KEY')) === '') {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'APP_KEY belum dikonfigurasi pada Environment Variables Vercel.';
    exit;
}

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$publicPath = realpath(__DIR__.'/../public');
$relativeRequestPath = ltrim($requestPath, '/');

if ($publicPath && preg_match('#^(build/|favicon\.ico$|robots\.txt$)#', $relativeRequestPath) === 1) {
    $assetPath = realpath($publicPath.'/'.$relativeRequestPath);
    $publicPrefix = rtrim($publicPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

    if ($assetPath && str_starts_with($assetPath, $publicPrefix) && is_file($assetPath)) {
        $extension = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
        ];

        header('Content-Type: '.($mimeTypes[$extension] ?? 'application/octet-stream'));
        header('Content-Length: '.filesize($assetPath));
        header(str_starts_with($requestPath, '/build/')
            ? 'Cache-Control: public, max-age=31536000, immutable'
            : 'Cache-Control: public, max-age=3600');
        readfile($assetPath);
        exit;
    }
}

if (file_exists($maintenance = $temporaryStorage.'/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
