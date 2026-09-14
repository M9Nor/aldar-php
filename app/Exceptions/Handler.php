<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Modules\Cms\Classes\ResponseHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        return parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Modules\Cms\Classes\ResponseHandler
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        switch (true) {

            case $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException:
                return parent::render($request, $exception);
                break;

            case $exception instanceof \Illuminate\Session\TokenMismatchException:
            case $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException:
            case $exception instanceof \Illuminate\Auth\AuthenticationException:
                return $this->asResponse($request, [
                    'success'       => false,
                    'type'          => 'warning',
                    'title'         => __('cms::messages.page_session_expired_please_try_again.title'),
                    'description'   => __('cms::messages.page_session_expired_please_try_again.description'),
                    'redirect_url'  => route('login')
                ]);
                break;

            // Handles Authorization exceptions.
            case $exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException:
            case $exception instanceof \Illuminate\Auth\Access\AuthorizationException:
            case $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException:
                return $this->asResponse($request, [
                    'success'       => false,
                    'type'          => 'warning',
                    'title'         => __('permissions::messages.permission_error.title'),
                    'description'   => __('permissions::messages.permission_error.description')
                ]);
                break;

            case $exception instanceof \Exception:
                return parent::render($request, $exception);
                return $this->asResponse($request, [
                    'success'       => false,
                    'type'          => 'danger',
                    'title'         => __('cms::messages.general_error.title'),
                    'description'   => __('cms::messages.general_error.description'),
                ]);
                break;

            default:

                return parent::render($request, $exception);
                break;
        }
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function asResponse($request, $data, $code = 200)
    {
        if(isset($data['redirect_url']))
        {
            $response =  redirect(url($data['redirect_url']))->with('toastr', $data);

            return $response;
        }

        $response = redirect()->back()->with('toastr', $data);

        if($code != 200)
        {
            $response = $response->withInput();
        }

        return $response;
    }
}
