<?php

namespace Modules\Cms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\Filesystem;
use Modules\Cms\Classes\ImageManipulator;
use League\Glide\ServerFactory;
use League\Glide\Server;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Server::class, function($app) {

            $filesystem = $app->make(Filesystem::class);

            return ServerFactory::create([
                'source'                    => $filesystem->getDriver(),
                'source_path_prefix'        => '',
                'cache'                     => $filesystem->getDriver(),
                'cache_path_prefix'         => '.cache',
                'driver'                    => 'gd',
                'defaults'                  =>  [
                    'fit'   => 'crop',
                    'fm'    => 'jpg',
                    'q'     => 75
                ],
                // Glide 3 names cache files with xxh3 over API params only; Glide 1.5 used md5 over all
                // params. This is Glide 1.5's Server::getCachePath() verbatim, so every image already
                // cached under .cache keeps its name. Glide binds $this to the Server when calling it,
                // so the closure must not be static.
                'cache_path_callable'       => function (string $path, array $params = []): string {
                    $sourcePath = $this->getSourcePath($path);

                    if ($this->sourcePathPrefix) {
                        $sourcePath = substr($sourcePath, strlen($this->sourcePathPrefix) + 1);
                    }

                    // Glide 1.5 getAllParams(): defaults, then presets named in p, then params, unfiltered.
                    $all = $this->defaults;

                    if (isset($params['p'])) {
                        foreach (explode(',', (string) $params['p']) as $preset) {
                            if (isset($this->presets[$preset])) {
                                $all = array_merge($all, $this->presets[$preset]);
                            }
                        }
                    }

                    $params = array_merge($all, $params);
                    unset($params['s'], $params['p']);
                    ksort($params);

                    $md5 = md5($sourcePath.'?'.http_build_query($params));

                    $cachedPath = $this->groupCacheInFolders ? $sourcePath.'/'.$md5 : $md5;

                    if ($this->cachePathPrefix) {
                        $cachedPath = $this->cachePathPrefix.'/'.$cachedPath;
                    }

                    if ($this->cacheWithFileExtensions) {
                        $ext = $params['fm'] ?? pathinfo($path, PATHINFO_EXTENSION);
                        $ext = ($ext === 'pjpg') ? 'jpg' : $ext;
                        $cachedPath .= '.'.$ext;
                    }

                    return $cachedPath;
                },
                // 'group_cache_in_folders' =>  // Whether to group cached images in folders
                // 'watermarks' =>              // Watermarks filesystem
                // 'watermarks_path_prefix' =>  // Watermarks filesystem path prefix
                // 'max_image_size' =>          // Image size limit
                // 'presets' =>                 // Preset image manipulations
                // 'base_url' =>                // Base URL of the images
                // 'response' =>                // Response factory
            ]);

        });

        $this->app->singleton('ImageManipulator', function($app) {
            $server = $this->app->make(Server::class);
            return new ImageManipulator($server);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
