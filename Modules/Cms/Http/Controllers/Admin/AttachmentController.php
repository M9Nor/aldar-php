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
            'attachment'        => $request->validation_rules ?? 'required',
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
                $subFolder = $request->sub_folder ?? 'general';
                if(!empty($subFolder))
                {
                    $subFolder = !Str::startsWith($subFolder, '/') ? "/{$subFolder}" : $subFolder;
                }
                if($attachmentUid = $request->attachment->storeAs('attachments'.$subFolder, $request->attachment->getClientOriginalName()))
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
