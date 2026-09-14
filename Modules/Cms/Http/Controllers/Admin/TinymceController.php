<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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
    public function __construct(){
        parent::__construct();
        $this->middleware('auth');
    }
    protected function generate_file_name($str = null){
        return str_replace(['$',' ','|','"','\'','/','\\', '?', '؟'], '', microtime() . $str);
    }
    public function uploader (Request $request){
        // if ( ! auth()->user()->can('TINYMCE_UPLOADER') )
        //     if ( ! auth()->user()->isAn('ROOT') ) return [
        //         'success' => false,
        //         'type'    => 'danger',
        //         'strong'  => __('cms::app.crud_messages.permission_error.title'),
        //         'msg'     => __('cms::app.crud_messages.permission_error.description')
        //     ];
        $stringLength = strlen( $request->tinymce['base64'] );
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
        $filename = $this->generate_file_name( $request->tinymce['filename'] );
        \Storage::disk('graph')->put( 'tinymce/' . $filename, base64_decode( $request->tinymce['base64'] ));
        return [
            'success'  => true,
            'msg'      => 'Uploaded Successfully',
            // 'location' => asset('graph/uploads/original/tinymce/' . $filename),
            'location' => '/graph/uploads/original/tinymce/' . $filename,
        ];
    }
}