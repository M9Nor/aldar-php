<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Cms\Entities\Attachment;
use Validator;
use Auth;
use DB;
use Modules\Cms\Http\Controllers\CmsController;

class TinymceController extends CmsController
{
    protected static $UploadValidation = [
        'base_64_kb' => 10241
    ];

    /** Accepted image types, detected from the file bytes, never from the client name. */
    public const ALLOWED_MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    public function __construct(){
        parent::__construct();
        $this->middleware('auth');
    }

    public function uploader (Request $request){
        $base64 = (string) data_get($request->input('tinymce'), 'base64', '');

        $stringLength = strlen( $base64 );
        $byte = 4 * ( $stringLength / 3 ) * 0.5624896334383812;
        $kb = $byte / 1024;
        if ( $kb > static::$UploadValidation['base_64_kb'] ) {
            return [
                'success' => false,
                'type'    => 'danger',
                'strong'  => __('cms::global.upload_size_error.title'),
                'msg'     => __('cms::global.upload_size_error.description', ['size' => static::$UploadValidation['base_64_kb'], 'unit' => 'kb']),
            ];
        }

        $binary = base64_decode($base64, true);
        $mime = $binary === false ? null : (new \finfo(FILEINFO_MIME_TYPE))->buffer($binary);

        if ( ! isset(self::ALLOWED_MIME_EXTENSIONS[$mime]) || @getimagesizefromstring($binary) === false ) {
            return [
                'success' => false,
                'type'    => 'danger',
                'strong'  => __('cms::app.crud_messages.upload_error.title'),
                'msg'     => __('cms::app.crud_messages.upload_error.description'),
            ];
        }

        $filename = Str::random(40) . '.' . self::ALLOWED_MIME_EXTENSIONS[$mime];
        try {
            \Storage::disk('graph')->put( 'tinymce/' . $filename, $binary );
        } catch (\League\Flysystem\FilesystemException $e) {
            // The graph disk throws on a failed write (S17): never report success for a file that is not there.
            report($e);

            return [
                'success' => false,
                'type'    => 'danger',
                'strong'  => __('cms::app.crud_messages.upload_error.title'),
                'msg'     => __('cms::app.crud_messages.upload_error.description'),
            ];
        }

        return [
            'success'  => true,
            'msg'      => 'Uploaded Successfully',
            'location' => '/graph/uploads/original/tinymce/' . $filename,
        ];
    }
}
