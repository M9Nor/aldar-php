<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Server;

class ImageController extends Controller
{
    /**
     * Cache an image and return it.
     * @var Server
     * @var Request
     * @return Response
     */
    public function show(Server $server, Request $request)
    {
        if(! $server->sourceFileExists($request->path))
        {
            abort(404);
        }

        if($request->size == 'original')
        {
            $server->setDefaults([]);

            $name = $server->makeImage($request->path, []);
            $file = Storage::get($name);
            $type = Storage::mimeType($name);

            return \Response::make($file, 200)->header("Content-Type", $type);
        }

        $size = preg_split('/x/', $request->size);

        $options = [];

        if($size[0] != 'auto') $options['w'] = $size[0];
        if($size[1] != 'auto') $options['h'] = $size[1];

        // Only known sizes are generated. A size that is already cached keeps working,
        // so no URL that rendered before this change can break.
        if(! in_array($request->size, config('image_sizes.allowed'), true) && ! $server->cacheFileExists($request->path, $options))
        {
            abort(404);
        }

        $name = $server->makeImage($request->path, $options);
        $file = Storage::get($name);
        $type = Storage::mimeType($name);

        return \Response::make($file, 200)->header("Content-Type", $type);
    }
}
