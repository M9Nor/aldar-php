<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Yajra\DataTables\Facades\DataTables;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Entities\UserType;
use Modules\Cms\Classes\ResponseHandler;
use Illuminate\Support\Str;
use Modules\Cms\Entities\Attachment;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\ExternalAttachments;

use App\Rules\Slug;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;

use Modules\Cms\Entities\Tag as CrudModel;
use Illuminate\Support\Arr;



class TagController extends CmsController
{
    public $attributeNames;

    /**
     * Get tags for user based on their input and return the input as well.
     * @param  Request $request
     * @return Response
     */
    public function __construct()
    {
        $this->attributeNames = [
            'text_ar'           => __('cms::areas.text_ar'),
            'text_en'           => __('cms::areas.text_en'),
            'text_tr'           => __('cms::areas.text_tr'),
            'image_ar'          => __('cms::areas.image_ar'),
            'image_en'          => __('cms::areas.image_en'),
            'image_tr'          => __('cms::areas.image_tr'),
            'description_ar'    => __('cms::areas.description_ar'),
            'description_en'    => __('cms::areas.description_en'),
            'description_tr'    => __('cms::areas.description_tr'),
            'keywords'          => __('cms::areas.keywords')
        ];
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function list(Request $request)
    {
        $term   = trim((string) $request->q);
        $tags   = CrudModel::with('translations')->when($request->locale, function($query, $locale) {
            $query->whereHas('translations', function($q) use ($locale) {
                $q->where('locale', $locale);
            });
        })->whereTranslationLike('text', "%{$term}%")->get()->map(function($item) {
            $item->text = $item->translate(request('locale', app()->getLocale()))->text;
            return $item;
        })->toArray();

        $max_id = CrudModel::max('id');
        if(!in_array($term, Arr::flatten($tags)))
        {
            $tags = Arr::prepend($tags, [
                'id'    => $max_id + $request->count, // To prevent duplicate IDs.
                'text'  => $term
            ]);
        }

        return json_encode($tags);
    }
    public function index(Request $request)
    {

        $this->authorize('view', CrudModel::class);
        return view('cms::admin.tags.index');
    }
    public function data(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        $list =  CrudModel::select(['cms_tags.*',
        DB::raw('
            (
                SELECT trans.text
                FROM cms_tag_translations AS trans
                    WHERE trans.tag_id = cms_tags.id
                    AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
            ')
        ])->with('translations')->withDisabled();

        $withTrashed = request('trashed', 'hide');
        // Exclude ROOT areas from query when the logged user is not ROOT.
        $list->when($withTrashed == 'show', function($query) {
            $query->onlyTrashed();
        });
        $datatables = DataTables::of($list);
        $datatables
        ->addIndexColumn() // Adds an incremental first row.
        ->filter(function($q) use ($request) {
            if(!empty($filter = $request->filter) && is_array($filter)){
                $filter = collect($filter)->mapWithKeys(function ($item) {
                    return [$item['name'] => $item['value']];
                });
                $q->when(!empty($filter['title']), function($query) use ($filter) {
                    $query->whereTranslationLike('text', "%{$filter['title']}%");
                })->when(!empty($filter['language']), function($query) use ($filter) {
                    $query->notTranslatedIn("{$filter['language']}");
                    // $query->whereHas('translations',function($q) use($filter){
                    //     $q->where('locale','!=',"%{$filter['language']}%");
                    // });
                    // $query->whereTranslationLike('locale', "%{$filter['language']}%");
                });
            }
        })
        ->addColumn('translated_name', function($model) {
            return !is_null( $model->new_title ) ? $model->new_title : $model->translations->first()->text;
        })
        ->addColumn('arabic', function($model){
            $output = '';

            foreach($model->translations->sortBy('locale') as $translation)
            {
                if($translation->locale == 'ar')
                {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                }
                $output .= '</div>';
            }
            return $output;
        })
        ->addColumn('english', function($model){
            $output = '';

            foreach($model->translations->sortBy('locale') as $translation)
            {
                if($translation->locale == 'en')
                {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                }
                $output .= '</div>';
            }
            return $output;
        })
        // ->addColumn('turkish', function($model){
        //     $output = '';

        //     foreach($model->translations->sortBy('locale') as $translation)
        //     {
        //         if($translation->locale == 'tr')
        //         {
        //             $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
        //         }
        //         $output .= '</div>';
        //     }
        //     return $output;
        // })
        ->addColumn('persian', function($model){
            $output = '';

            foreach($model->translations->sortBy('locale') as $translation)
            {
                if($translation->locale == 'fa')
                {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                }
                $output .= '</div>';
            }
            return $output;
        })
        ->addColumn('actions', function($model){
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            if(!$model->trashed())
            {
                if(auth()->user()->can('update', $model))
                {
                    $items[] = array_merge($this->actions['edit'], [
                        'url'   => route('TagController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }

                if(auth()->user()->can('delete', $model) )
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('TagController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model))
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('TagController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model))
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('TagController@destroy', ['model' => $model->id]),
                        'id'    => 'force_delete_' . $model->id
                    ]);
                }
            }
            if(count($items) > 1)
            {
                $actions['dropdown'] = $items;
            }
            else
            {
                $actions['icons'] = $items;
            }

            return $actions;
        });
        $rawColumns = [];
        $rawColumns[] = 'tag';
        $rawColumns[] = 'actions';
        $rawColumns[] = 'arabic';
        $rawColumns[] = 'english';
        $rawColumns[] = 'turkish';
        $rawColumns[] = 'persian';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function save(Request $request)
    {
        // Saving a keyword creates a tag: the tag create page's ability, before validation (S21).
        $this->authorize('create', CrudModel::class);
        $this->attributeNames = [
            'attachment' => __('cms::areas.fields.text.label'),
        ];
        $rules = [
            'keyword.id'    => 'required',
            'keyword.text'  => 'string|max:45',
        ];
        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);
        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }
        try {
            DB::transaction(function () use ($request) {
                $this->data['tag'] = CrudModel::whereTranslation('text', $request->keyword['text'])->firstOrNew([
                    'id'        => $request->keyword['id']
                ], [
                    'added_by'  => auth()->user()->id
                ]);
                $this->data['tag']->{'text:' . request('locale', request('locale', app()->getLocale()))} = $request->keyword['text'];
                $this->data['tag']->save();
            }, 5);
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.save_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.save_error.description')
            ], 409);
        }
        $this->data['tag']->t_text = $this->data['tag']->translate(request('locale', app()->getLocale()))->text;
        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.save_success.title'),
            'description'   => __('cms::messages.save_success.description'),
            'tag'           => $this->data['tag']
        ]);
    }
    public function create(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        return view('cms::admin.tags.create');
    }
    public function store(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        // Check if the authenticated user is allowed to proceed farther.
        $rules = [
            'text_ar'           => 'required|string|max:45',
            'image_ar'          => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'description_ar'    => 'nullable|string|max:10000',
            'keywords'          => 'nullable|string|max:5000',
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('text_'.$locale))){
                    $rules['text_'.$locale]         =   'nullable|string|max:45';
                    $rules['image_'.$locale]        =   'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['description_'.$locale]  =   'nullable|string|max:10000';
                }
            }
        }
        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);
        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'validation_error',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']                = new CrudModel;
                $this->data['model']->type          = 'tags';
                $this->data['model']->keywords      = $request->keywords;
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('text_'.$locale))){
                        $this->data['model']->{'text:'. $locale} = $request->input('text_'.$locale);
                        $this->data['model']->{'description:'. $locale} = $request->input('description_'.$locale);
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('tags');
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                    }
                }
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function edit(Request $request)
    {
        $this->data['model'] = CrudModel::with('translations')->withTrashed()->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        return view('cms::admin.tags.update', $this->data);
    }
    public function update(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail("{$request->model}");
        $this->authorize('update', $this->data['model']);
        $rules = [
            'text_ar'      => 'required|string|max:45',
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('text_ar'.$locale))){
                    $rules['text_ar'.$locale]        = (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'nullable').'|string|max:45';
                }
            }
        }
        // foreach($this->data['model']->translations as $trans){
        //     if($trans->locale != 'ar'){
        //         if( is_null($request->input('text_'.$trans->locale)) ){
        //             $rules['text_'.$trans->locale] = 'required|string|max:45';
        //         }
        //     }
        // }
        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);
        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'validation_error',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->keywords      = $request->keywords;
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('text_'.$locale))){
                        $this->data['model']->{'text:'. $locale}            = $request->input('text_'.$locale);
                        $this->data['model']->{'description:'. $locale}     = $request->input('description_'.$locale);
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('tags');
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                    }
                }
                $this->data['model']->save();
            });
        }
        catch (\Exception $e) {
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id
            ]
        ]);
    }
    public function destroy(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('delete', $this->data['model']);

        if(!$this->data['model']->trashed())
        {
            try {
                DB::transaction(function() use ($request) {
                    $this->data['model']->delete();
                });
            } catch (\Exception $e) {
                return response()->json([
                    'success'     => false,
                    'type'        => 'danger',
                    'title'       => __('cms::messages.delete_error.title'),
                    'description' => __('cms::messages.delete_error.description')
                ]);
            }

            return response()->json([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.delete_success.title'),
                'description' => __('cms::messages.delete_success.description')
            ]);
        }

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('forceDelete', $this->data['model']);

        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->forceDelete();
                if($this->data['model']->image) app()->ImageManipulator->deleteImage($this->data['model']->image);
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.delete_error.title'),
                'description' => __('cms::messages.delete_error.description')
            ]);
        }

        return response()->json([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.delete_success.title'),
            'description' => __('cms::messages.delete_success.description')
        ]);
    }
    public function restore(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);
        $this->authorize('restore', $this->data['model']);
        if(!$this->data['model']->trashed())
        {
            return response()->json([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.restore_error.title'),
                'description' => __('cms::messages.restore_error.description')
            ]);
        }

        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->restore();
            });
        } catch (\Exception $e) {
            return response()->json([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.restore_error.title'),
                'description' => __('cms::messages.restore_error.description')
            ]);
        }

        return response()->json([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.restore_success.title'),
            'description' => __('cms::messages.restore_success.description')
        ]);
    }
    public function massDestroy(Request $request)
    {
        $this->data['models'] = CrudModel::withTrashed()->whereIn('id', explode(',', $request->ids))->get();

        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'full_name';
        $this->data['failed'] = collect([]);

        // Loop through the selected models to determine which of whom can be deleted.
        foreach($this->data['models'] as $model)
        {
            if(!$model->trashed())
            {
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('delete', $model);

                    DB::transaction(function() use ($request, $model) {
                        $model->delete();
                    });

                } catch (\Exception $e) {
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }
            }
            else
            {
                try {
                    $this->authorize('forceDelete', $model);
                    DB::transaction(function() use ($request, $model) {
                        $model->forceDelete();
                        if($model->image) app()->ImageManipulator->deleteImage($model->image);
                    });
                } catch (\Exception $e) {
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }

            }
        }

        if($this->data['failed']->count() > 0)
        {
            $failed = '';
            foreach($this->data['failed'] as $item)
            {
                $failed .= '<span class="kt-badge kt-badge--dark kt-badge--inline">' . $item . '</span> ';
            }
            return response()->json([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_delete_error.title'),
                'description' => __('cms::messages.mass_delete_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }
        else
        {
            return response()->json([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_delete_success.title'),
                'description' => __('cms::messages.mass_delete_success.description')
            ]);
        }
    }
    public function massRestore(Request $request)
    {
        $this->data['models'] = CrudModel::onlyTrashed()->whereIn('id', explode(',', $request->ids))->get();

        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'full_name';
        $this->data['failed'] = collect([]);

        // Loop through the selected models to determine which of whom can be restored.
        foreach($this->data['models'] as $model)
        {
            try {
                // Check if the authenticated user is allowed to proceed farther.
                $this->authorize('restore', $model);

                DB::transaction(function() use ($request, $model) {
                    $model->restore();
                });

            } catch (\Exception $e) {
                // Push failed models to failed array to notify the user which models could not be restored.
                $this->data['failed']->push($model->{$this->data['attribute']});
            }
        }

        if($this->data['failed']->count() > 0)
        {
            $failed = '';

            foreach($this->data['failed'] as $item)
            {
                $failed .= '<span class="kt-badge kt-badge--dark kt-badge--inline">' . $item . '</span> ';
            }

            return response()->json([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_restore_error.title'),
                'description' => __('cms::messages.mass_restore_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }
        else
        {
            return response()->json([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_restore_success.title'),
                'description' => __('cms::messages.mass_restore_success.description')
            ]);
        }
    }
}
