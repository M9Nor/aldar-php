<?php

namespace Modules\Notification\Http\Controllers;

use DB;
use Hash;
use Validator;
use Exception;
use Bouncer;
use DataTables;
use Carbon\Carbon;

use Modules\Notification\Http\Responses\CrudResponse;
use Modules\Cms\Classes\ResponseHandler;
use Modules\Notification\Entities\FirebaseToken;
use Modules\Notification\Entities\FirebaseNotification;
use Modules\Notification\Entities\FirebaseNotificationReceiver;
use Modules\Permissions\Entities\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Notification\Http\Controllers\AdminBaseController;

class NotificationController extends AdminBaseController
{
    public $attributeNames;

    public function __construct()
    {
        $this->attributeNames = [
            'link'  => __('notification::strings.fields.notification_link.label'),
            'title' => __('notification::strings.fields.notification_title.label'),
            'body'  => __('notification::strings.fields.notification_body.label'),
        ];

        $this->middleware('auth')->except(['getConfig', 'postWebToken']);
        parent::__construct();
    }

    /** Attempts per client IP per minute on the public postWebToken endpoint (S6). */
    public const WEB_TOKEN_MAX_ATTEMPTS = 10;

    public static $validationsRules = [
        'postWebToken' => [
            // notif_tokens.token is varchar(191).
            'data_token' => 'required|string|max:191',
        ],
    ];

    public function getConfig(Request $request)
    {
        return [
            'apiKey'            => config('notification.firebase.apiKey'),
            'messagingSenderId' => config('notification.firebase.messagingSenderId'),
            'authDomain'        => config('notification.firebase.authDomain'),
            'databaseURL'       => config('notification.firebase.databaseURL'),
            'projectId'         => config('notification.firebase.projectId'),
            'appId'             => config('notification.firebase.appId'),
            'measurementId'     => config('notification.firebase.measurementId'),
        ];
    }

    public function postWebToken(Request $request)
    {
        // Public endpoint: count every attempt per client IP and refuse with a JSON 429 over the limit (S6).
        // Not the throttle middleware: app/Exceptions/Handler.php turns its ThrottleRequestsException,
        // like every HttpException, into a redirect to the login page.
        // Carries H5's TrustProxies caveat, same as the sibling contact-form limiter: TrustProxies.php
        // leaves $proxies = null, so behind Hostinger's CDN $request->ip() is REMOTE_ADDR, not the
        // visitor's address, and every visitor shares one quota until Phase 3 probes how the client IP
        // reaches PHP and sets $proxies deliberately (a careless '*' would make X-Forwarded-For spoofable
        // and the limit bypassable instead).
        $throttleKey = 'notification-web-token:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, self::WEB_TOKEN_MAX_ATTEMPTS)) {
            return response()->json([
                'success'             => false,
                'type'                => 'toastr',
                'message_type'        => 'error',
                'message_title'       => __('admin::strings.error.title'),
                'message_description' => __('admin::strings.error.description'),
                'errors'              => [],
            ], 429);
        }
        RateLimiter::hit($throttleKey, 60);

        $this->data['locale']      = $request->locale ? $request->locale : app()->getLocale();
        $this->data['CurrentUser'] = $request->user();
        $this->data['_VALIDATOR_'] = Validator::make(
            $request->all(), static::$validationsRules['postWebToken']
        );
        $this->data['_VALIDATOR_']->validate();

        try {
            $this->data['model'] = FirebaseToken::firstOrNew([
                'token' => $request->data_token
            ]);
            DB::transaction(function() use ($request) {
                $this->data['model']->user_id       = !is_null($this->data['CurrentUser']) ? $this->data['CurrentUser']->id : null;
                $this->data['model']->token         = $request->data_token;
                $this->data['model']->last_activity = Carbon::now()->toDateTimeString();
                $this->data['model']->save();
            });
        }
        catch (Exception $e) {
            // Logged server-side only: the client never sees exception details (S6).
            report($e);

            return new CrudResponse([
                'success'             => false,
                'type'                => 'toastr',
                'message_type'        => 'error',
                'message_title'       => __('admin::strings.error.title'),
                'message_description' => __('admin::strings.error.description'),
                'errors'              => [],
            ], 500);
        }

        return new CrudResponse([
            'success'             => true,
            'type'                => 'toastr',
            'message_type'        => 'success',
            'message_title'       => __('admin::strings.save_success.title'),
            'message_description' => __('admin::strings.save_success.description'),
            'errors'              => []
        ], 200);
    }

    public function getList(Request $request)
    {
        // The notifications ability (S21).
        $this->authorize('view', FirebaseNotification::class);
        $User     = auth()->user();
        $Receivers = FirebaseNotificationReceiver::where(function($query)use($User){
            $query->where('to_user_id', $User->id);
            $query->orWhereIn('group', $User->roles->pluck('name')->toArray());
            $query->orWhereNull('group');
        })
        ->with('Notification.translations', 'Data')
        ->with([
            'FromUser' => function($FromUser){
                $FromUser->withDisabled()->withTrashed();
            }
        ])
        ->orderBy('created_at', 'DESC')
        ->limit(75)
        ->get();
        $Result = [];
        foreach ($Receivers as $key => $Receiver) {
            if (empty($Receiver->Notification)) {
                continue;
            }
            $Result[] = FirebaseNotification::prepareToSend($Receiver->Notification, $Receiver, app()->getLocale());
        }
        $NewCount = FirebaseNotificationReceiver::where('to_user_id', $User->id)->where('status', 'DELIVERED')->count();
        FirebaseNotificationReceiver::where('to_user_id', $User->id)->where('status', 'DELIVERED')->update([
            'status' => 'SEEN',
        ]);
        return [
            'notifications' => $Result,
            'new_count'     => $NewCount,
        ];
    }

    public function index(Request $request)
    {
        // The page extends a view namespace (admin::) that does not exist, so it could only answer 500, and its
        // menu entry is commented out. 404 instead (S11); the route name stays for notifications/create.blade.php.
        abort(404);
    }

    public function postIndex(Request $request)
    {
        // The notifications ability (S21).
        $this->authorize('view', FirebaseNotification::class);
        $this->data['Roles'] = Role::with('translations')->get();

        $this->data['DataTable_Q'] = FirebaseNotification::select([
            '*',
        ])
        ->where('added_by', 'USER')
        ->with('translations');

        $this->data['DataTable_Q']->AdvancedSearch($request->advanced_search);

        return DataTables::of(
            $this->data['DataTable_Q']
        )
        ->filter(function($query)use($request){
            if (!empty( $request->search['value'] )) {
                $query->where(function($Q)use($request){
                    $Q->whereHas('translations', function($translations)use($request){
                        $translations->where(function($q)use($request){
                            $q->where('title', 'like', "%{$request->search['value']}%");
                            $q->orWhere('body', 'like', "%{$request->search['value']}%");
                        });
                    });
                });
            }
        })
        ->addColumn('title', function($Row){
            return $Row->__('title', app()->getLocale());
        })
        ->addColumn('body', function($Row){
            return strip_tags($Row->__('body', app()->getLocale()));
        })
        ->addColumn('type_data', function($Row){
            $res  = [];
            $type = $this->data['Roles']->where('name', $Row->group)->first();
            if ($type) {
                try {
                    $res = $type->toArray();
                    $res['label_text']  = $type->__('title');
                    $res['label_color'] = 'warning';
                }
                catch (Exception $th) {
                    //throw $th;
                }
            }
            else {
                $res['label_text']  = __('admin::strings.all_group');
                $res['label_color'] = 'danger';
            }
            return $res;
        })
        ->addColumn('actions', function($Row){
            $DATA         = [];
            $BASE_ACTIONS = [];
            if (! empty($BASE_ACTIONS)) {
                $DATA[] = [
                    'type'  => 'dropdown',
                    'icon'  => 'la la-gear',
                    'items' => $BASE_ACTIONS,
                ];
            }
            return $DATA;
        })
        ->toJson();
    }

    public function create(Request $request)
    {
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('create', FirebaseNotification::class);

        $this->data['roles'] = Role::with('translations');

        if(auth()->user()->isNotA('ROOT'))
        {
            $this->data['roles']->where('name', '!=', 'ROOT');
        }

        $this->data['roles'] = $this->data['roles']->orderBy('id', 'desc')->get();

        return view('notification::notifications.create', $this->data);
    }

    public function postCreate(Request $request)
    {
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('create', FirebaseNotification::class);

        $rules = [
            'title' => 'required|max:191',
            'body'  => 'required|max:2500',
            'link'  => 'nullable|url',
            'image'  => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'group' => 'nullable',
        ];

        $this->data['locale']      = $request->locale ? $request->locale : app()->getLocale();
        $this->data['CurrentUser'] = $request->user();
        $validator = Validator::make(
            $request->all(), $rules, [], $this->attributeNames
        );

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
            $this->data['model'] = new FirebaseNotification;
            DB::transaction(function()use($request){
                $this->data['model']->type                               = Carbon::now()->format('Y-m-d-H-i-s');
                $this->data['model']->added_by                           = 'USER';
                $this->data['model']->link                               = empty($request->link)  ? null   : $request->link;
                $this->data['model']->group                              = empty($request->group) ? 'BASE' : $request->group;
                $this->data['model']->{'title:' . $this->data['locale']} = $request->title;
                $this->data['model']->{'body:' . $this->data['locale']}  = $request->body;
                $this->data['model']->{'icon:' . $this->data['locale']}  = 'logo11.png';
                if($request->hasFile('image_'.$this->data['locale'])){
                    ${'imagePath'.$this->data['locale']}                          = $request->file('image_'.$this->data['locale'])->store('firebase');
                    $this->data['model']->{'image:'.$this->data['locale']}        = ${'imagePath'.$this->data['locale']};
                }
                $this->data['model']->{'sound:' . $this->data['locale']} = 'default.mp3';
                $this->data['model']->created_by                         = $request->user()->id;
                $this->data['model']->save();

                $UserToNotify                   = new FirebaseNotificationReceiver;
                $UserToNotify->notification_id  = $this->data['model']->id;
                $UserToNotify->status           = 'DELIVERED';
                $UserToNotify->triggered_by     = 'USER';
                $UserToNotify->from_user_id     = null;
                $UserToNotify->to_user_id       = null;
                $UserToNotify->group            = empty($request->group) ? null : $request->group;
                $UserToNotify->save();
            });
            try {
                app()->make('Cms')->startSendingNotifications();
            }
            catch (\Exception $th) {
                return new ResponseHandler([
                    'success'       => false,
                    'type'          => 'danger',
                    'title'         => __('cms::messages.send_error.title'),
                    'description'   => config('debug.enabled') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.send_error.description')
                ]);
            }
        }catch(Exception $e) {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.send_error.title'),
                'description'   => true ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.send_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.send_success.title'),
            'description'   => __('cms::messages.send_success.description'),
            // 'redirect_url'  => request('redirect_url', 'javascript:;')
        ]);
    }
}
