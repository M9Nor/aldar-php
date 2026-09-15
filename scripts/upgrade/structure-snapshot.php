<?php
/**
 * Structural baseline for the Laravel 7 -> 13 upgrade. Runs unchanged on both versions.
 *
 *   php scripts/upgrade/structure-snapshot.php routes   every route: methods, uri, name, action, route middleware
 *   php scripts/upgrade/structure-snapshot.php config   selected non-secret config values, paths relative to the app root
 *   php scripts/upgrade/structure-snapshot.php glide    Glide cache paths for fixed inputs (proves cached images stay valid)
 *
 * Output is sorted, pretty-printed JSON on stdout. Never add secrets (keys, passwords, tokens) to the config list.
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$base = rtrim(base_path(), '/');
$relative = function ($value) use (&$relative, $base) {
    if (is_array($value)) {
        return array_map($relative, $value);
    }
    return is_string($value) ? str_replace($base, '{base}', $value) : $value;
};

switch ($argv[1] ?? '') {
    case 'routes':
        $out = [];
        foreach ($app['router']->getRoutes() as $route) {
            $out[] = [
                'methods'    => array_values(array_diff($route->methods(), ['HEAD'])),
                'uri'        => $route->uri(),
                'name'       => $route->getName(),
                'action'     => ltrim($route->getActionName(), '\\'),
                // A route's middleware() can hold a Closure (route-defined inline middleware)
                // instead of a string name. strval() on a Closure fatals on both PHP 7.4 and
                // 8.4 ("Object of class Closure could not be converted to string"), so map
                // anything that is not already a string to the literal 'Closure' instead.
                'middleware' => array_values(array_map(function ($middleware) {
                    return is_string($middleware) ? $middleware : 'Closure';
                }, $route->middleware())),
            ];
        }
        usort($out, function ($a, $b) {
            return [$a['uri'], implode('|', $a['methods'])] <=> [$b['uri'], implode('|', $b['methods'])];
        });
        break;

    case 'config':
        $keys = [
            'app.locale', 'app.fallback_locale', 'app.timezone', 'app.cipher',
            'auth.defaults.guard', 'auth.providers.users.model',
            'cache.default', 'cache.prefix',
            'database.default',
            'filesystems.default', 'filesystems.disks.public.root', 'filesystems.disks.graph.root',
            'hashing.driver', 'hashing.bcrypt.rounds',
            'laravellocalization.hideDefaultLocaleInURL', 'laravellocalization.useAcceptLanguageHeader',
            'modules.activators.file.statuses-file',
            'session.driver', 'session.cookie', 'session.lifetime', 'session.path', 'session.domain',
            'session.secure', 'session.http_only', 'session.same_site', 'session.serialization',
            'translatable.locales', 'translatable.fallback_locale',
        ];
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = $relative(config($key));
        }
        $out['laravellocalization.supportedLocales(keys)'] = array_keys(config('laravellocalization.supportedLocales'));
        ksort($out);
        break;

    case 'glide':
        $server = $app->make(League\Glide\Server::class);
        $samples = [
            ['articles/parity-sample.png', ['w' => '1000', 'h' => '750']],
            ['articles/parity-sample.png', ['h' => '400']],
            ['projects/parity-sample.jpg', ['w' => '360', 'h' => '240']],
        ];
        $out = [];
        foreach ($samples as [$path, $params]) {
            $out[] = ['path' => $path, 'params' => $params, 'cachePath' => $server->getCachePath($path, $params)];
        }
        break;

    default:
        fwrite(STDERR, "usage: php scripts/upgrade/structure-snapshot.php <routes|config|glide>\n");
        exit(64);
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
