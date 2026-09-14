<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Backend\Entities\ContactUs;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('frontend::pages.contact_us.index');
    }
    public function stories()
    {
        return view('frontend::includes.commons.amp_stories');
    }
    public function store(Request $request)
    {
        // $rules = [
        //     'fullname'      => 'required|string|max:191',
        //     'phone'         => 'required|string|max:30',
        //     'email'         => 'required|email',
        //     'description'   => 'required|string|max:191',
        // ];

        // $validator = Validator::make($request->all(), $rules, [], [
        //     'fullname' => __('frontend::main.contact_form.name')
        // ])->validate();

        $validator = \Validator::make($request->all(), [
            'fullname'          => 'required|string|max:100|min:3',
            'phone'             => 'required|digits_between:1,15|numeric|max:999999999999999|min:1',
            'email'             => 'required|email|max:100|min:6',
            'description'       => 'required|string|max:1000|min:3',
            'country_code'      => 'required|string|max:25|min:1',
            
        ]);
        if ($validator->fails()) {
            $errors = [];
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $value) {
                $errors[$key] = $value[0];
            }
            $toReturn['data']['error'] = $errors;
            return response()->json($errors, 200);
        }

        try {
            \DB::transaction(function() use ($request) {
                $phone = $request->country_code . $request->phone;
                ContactUs::create(
                    [
                        'sender'        => "{$request->fullname}",
                        'email'         => "{$request->email}",
                        'phone'         => $phone,
                        'description'   => "{$request->description}",
                        'link'          => url()->previous(),
                    ]
                );
            });
        } catch (\Exception $e) {
            // return redirect()->back();
            return response()->json([
                'success'           => false,
                'message'           => trans('frontend::main.some_errors_occurred')
            ]);
        }
        return response()->json([
            'success'   => true,
            'disabled'  => true,
            'message'   => trans('frontend::main.sendded'),
            // 'redirect_url'=> route('PropertyController@confirmation'),
        ]);

        // return redirect()->back()->withSuccess(__('frontend::main.popup_messages.success'));


    }
    public function storeInner(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'fullname'          => 'required|string|max:100|min:3',
            'phone'             => 'required|digits_between:1,15|numeric|max:999999999999999|min:1',
            'email'             => 'required|email|max:100|min:6',
            'description'       => 'required|string|max:1000|min:3',
            'country_code'      => 'required|string|max:25|min:1',
            'language'          => 'required|string|max:100|min:1',
            'time_from'         => 'required|string|max:100',
            'time_to'           => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            $errors = [];
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $value) {
                $errors[$key] = $value[0];
            }
            $toReturn['data']['error'] = $errors;
            return response()->json($errors, 200);
        }
        // dd($request->all());
        try {
            \DB::transaction(function() use ($request) {
                $phone = $request->country_code . $request->phone;
                ContactUs::create(
                    [
                        'sender'        => "{$request->fullname}",
                        'email'         => "{$request->email}",
                        'phone'         => $phone,
                        'description'   => "{$request->description}",
                        'language'      => "{$request->language}",
                        'time_from'     => "{$request->time_from}",
                        'time_to'       => "{$request->time_to}",
                        'link'          => url()->previous(),
                    ]
                );
            });
        } catch (\Exception $e) {
            // return redirect()->back();
            return response()->json([
                'success'           => false,
                'message'           => trans('frontend::main.some_errors_occurred')
            ]);
        }
        return response()->json([
            'success'   => true,
            'disabled'  => true,
            'message'   => trans('frontend::main.sendded'),
            // 'redirect_url'=> route('PropertyController@confirmation'),
        ]);

        // return redirect()->back()->withSuccess(__('frontend::main.popup_messages.success'));


    }
    
    public function subscribe(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'emails'             => 'required|email|max:100|min:6',
        ]);

        if ($validator->fails()) {
            $errors = [];
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $value) {
                $errors[$key] = $value[0];
            }
            $toReturn['data']['error'] = $errors;
            return response()->json($errors, 200);
        }

        try {
            \DB::transaction(function() use ($request) {
                ContactUs::create(
                    [
                        'email'         => "{$request->emails}",
                        'link'          => url()->previous(),
                    ]
                );
            });
        } catch (\Exception $e) {
            // return redirect()->back();
            return response()->json([
                'success'           => false,
                'message'           => trans('frontend::main.some_errors_occurred')
            ]);
        }
        return response()->json([
            'success'   => true,
            'disabled'  => true,
            'message'   => trans('frontend::main.sendded'),
            // 'redirect_url'=> route('PropertyController@confirmation'),
        ]);

    }

    // public function storeVisit(Request $request)
    // {
        
    //     $validator = \Validator::make($request->all(), [
    //         'type_of_visit'     => 'required|string|max:100|min:3',
    //         'full_name'         => 'required|string|max:100|min:3',
    //         'phone_number'      => 'required|digits_between:1,15|numeric|max:999999999999999|min:1',
    //         'country_code'      => 'required|string|max:25|min:1',

    //         'residency_address' => 'required|string|max:255|min:3',
    //         'nationality'       => 'required|string|max:255|min:3',
    //         'native_language'   => 'required|string|max:255|min:3',
    //         'budget'            => 'required|string|max:255|min:3',
    //         'type_of_residency' => 'required|string|max:255|min:3',
            
    //     ]);
    //     if ($validator->fails()) {
    //         $errors = [];
    //         $messages = $validator->messages()->toArray();
    //         foreach ($messages as $key => $value) {
    //             $errors[$key] = $value[0];
    //         }
    //         $toReturn['data']['error'] = $errors;
    //         return response()->json($errors, 200);
    //     }
    //     try {
    //         \DB::transaction(function() use ($request) {
    //             $phone_number           = $request->country_code . $request->phone_number;
    //             ContactUs::create(
    //                 [
    //                     'type_of_visit'         => "{$request->type_of_visit}",
    //                     'sender'                => "{$request->full_name}",
    //                     'phone'                 => $phone_number,
    //                     'residency_address'     => "{$request->residency_address}",
    //                     'nationality'           => "{$request->nationality}",
    //                     'native_language'       => "{$request->native_language}",
    //                     'budget'                => "{$request->budget}",
    //                     'type_of_residency'     => "{$request->type_of_residency}",
    //                     'link'                  => url()->previous(),
    //                 ]
    //             );
    //         });
    //     } catch (\Exception $e) {
    //         // dd($e->getMessage());
    //         return response()->json([
    //             'success'           => false,
    //             'message'           => trans('frontend::main.some_errors_occurred')
    //         ]);
    //     }
    //     return response()->json([
    //         'success'   => true,
    //         'disabled'  => true,
    //         'message'   => trans('frontend::main.sendded'),
    //         'redirect_url'=> route('PropertyController@confirmation'),
    //     ]);

    //     // return redirect()->back()->withSuccess(__('frontend::main.popup_messages.success'));


    // }

}
