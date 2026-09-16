<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use League\Flysystem\WhitespacePathNormalizer;
use League\Glide\ServerFactory;
use League\Glide\Server;

class ImageController extends Controller
{
    /**
     * Cache an image and return it.
     * @param Server $server
     * @param Request $request
     * @return Response
     */
    public function show(Server $server, Request $request)
    {
        // Route parameters only: $request->path and $request->size read the query string first.
        $path = (string) $request->route('path');
        $size = (string) $request->route('size');

        if(! $this->isCanonicalPath($path))
        {
            abort(404);
        }

        if(! $server->sourceFileExists($path))
        {
            abort(404);
        }

        if($size == 'original')
        {
            $server->setDefaults([]);

            $name = $server->makeImage($path, []);
            $file = Storage::get($name);
            $type = Storage::mimeType($name);

            return \Response::make($file, 200)->header("Content-Type", $type);
        }

        $dimensions = preg_split('/x/', $size);

        $options = [];

        if($dimensions[0] != 'auto') $options['w'] = $dimensions[0];
        if($dimensions[1] != 'auto') $options['h'] = $dimensions[1];

        // Only known sizes are generated. A size that is already cached keeps working,
        // so no URL that rendered before this change can break.
        if(! in_array($size, config('image_sizes.allowed'), true) && ! $server->cacheFileExists($path, $options))
        {
            abort(404);
        }

        $name = $server->makeImage($path, $options);
        $file = Storage::get($name);
        $type = Storage::mimeType($name);

        return \Response::make($file, 200)->header("Content-Type", $type);
    }

    /**
     * Glide hashes the path it is given into the cache key, while Flysystem normalises that
     * path on every read and write. Any alias of a real file (dot segments, empty segments,
     * backslashes, escapes that Glide decodes, files under .cache) would get a fresh cache
     * entry per spelling, so only the one canonical spelling is served.
     */
    private function isCanonicalPath(string $path): bool
    {
        // Glide rawurldecode()s the path, so a remaining escape is an alias too.
        if ($path === '' || strpos($path, '\\') !== false || rawurldecode($path) !== $path) {
            return false;
        }

        try {
            $canonical = (new WhitespacePathNormalizer())->normalizePath($path) === $path;
        } catch (FilesystemException $e) {
            // Flysystem 3 throws PathTraversalDetected / CorruptedPathDetected where Flysystem 1 threw LogicException.
            $canonical = false;
        }

        return $canonical && ! preg_match('#(^|/)\.#', $path);
    }
}
