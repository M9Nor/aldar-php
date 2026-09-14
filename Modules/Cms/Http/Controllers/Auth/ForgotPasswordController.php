<?php

namespace Modules\Cms\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Modules\Cms\Classes\ResponseHandler;
use Auth;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('cms::auth.passwords.email');
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

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
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::auth.messages.send_reset_link_succeeded.title'),
            'description'   => trans($response)
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return new ResponseHandler([
            'success'       => false,
            'type'          => 'validation_error',
            'title'         => __('cms::auth.messages.send_reset_link_failed.title'),
            'description'   => trans($response)
        ], 401);
    }
}
