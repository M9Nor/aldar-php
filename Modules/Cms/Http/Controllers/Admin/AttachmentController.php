<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Cms\Classes\ResponseHandler;
use Modules\Cms\Entities\Attachment;
use Illuminate\Support\Str;
use Validator;
use Storage;
use DB;

class AttachmentController extends Controller
{
    /**
     * Rule strings the admin views send (dropzone components, Content, projects and
     * opportunities media). Anything else from the client falls back to DEFAULT_RULES.
     */
    public const ALLOWED_RULES = [
        'required|file|max:2048|mimes:jpeg,jpg,png,pdf',
        'required|file|max:2048|mimes:jpeg,jpg,png',
        'required|file|max:1024|mimes:jpeg,jpg,png,pdf',
        'required|image|max:1024|mimes:jpeg,jpg,png',
        'bail|required|image|max:2048|mimes:jpeg,jpg,png|dimensions:min_width=250,min_height=500,max_width=1000,max_height=2000',
    ];

    public const DEFAULT_RULES = 'required|file|max:2048|mimes:jpeg,jpg,png,pdf';

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->attributeNames = [
            'attachment' => __('cms::global.attachment'),
        ];

        $rules = [
            'attachment'        => in_array($request->validation_rules, self::ALLOWED_RULES, true) ? $request->validation_rules : self::DEFAULT_RULES,
            'attachable_id'     => 'nullable',
            'attachable_type'   => 'nullable|string|max:191'
        ];

        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);

        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.upload_error.title'),
                'description'   => __('cms::messages.upload_error.description', ['filename' => $request->attachment->getClientOriginalName()]),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }

        try {
            DB::transaction(function() use ($request) {
                $subFolder = preg_match('/^[a-z0-9_-]+$/i', (string) $request->sub_folder) ? $request->sub_folder : 'general';
                $storedName = Str::random(40) . '.' . ($request->attachment->guessExtension() ?: 'bin');
                if($attachmentUid = $request->attachment->storeAs("attachments/{$subFolder}", $storedName))
                {
                    $toAttach = [
                        'type'              => 'TEMP',
                        'uploaded_by'       => auth()->user()->id,
                        'filename'          => $request->attachment->getClientOriginalName(),
                        'uid'               => $attachmentUid,
                        'size'              => $request->attachment->getSize(),
                        'mime'              => $request->attachment->getMimeType(),
                        'input_name'        => $request->input_name ?? null
                    ];

                    if($request->attachable_id && $request->attachable_id != 'null') $toAttach['attachable_id'] = $request->attachable_id;
                    if($request->attachable_type) $toAttach['attachable_type'] = $request->attachable_type;

                    $this->data['attachment'] = Attachment::create($toAttach);
                }
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.save_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.save_error.description')
            ], 409);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.upload_success.title'),
            'description'   => __('cms::messages.upload_success.description', ['filename' => $request->attachment->getClientOriginalName()]),
            'attachment'    => $this->data['attachment']->id
        ]);
    }

    /**
     * Delete attachment from storage.
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'file_id' => 'nullable|exists:cms_attachments,id',
        ];

        $validator = Validator::make($request->all(), $rules, []);

        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray(),
            ], 422);
        }

        try {
            DB::transaction(function() use ($request) {
                $this->data['attachment'] = Attachment::find($request->file_id);
                if($this->data['attachment']) $this->data['attachment']->delete();
            });

            if($this->data['attachment']) app()->ImageManipulator->deleteImage($this->data['attachment']->uid);
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ], 409);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description')
        ]);
    }
}
