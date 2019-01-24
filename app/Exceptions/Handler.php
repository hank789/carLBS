<?php

namespace App\Exceptions;

use App\Traits\CreateJsonResponseData;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ApiValidationException::class,
        ApiException::class,
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $dev_should_reports = [
            ApiValidationException::class,
            ApiException::class,
        ];
        if(config('app.dev') != 'production'){
            foreach($dev_should_reports as $dev_should_report){
                if($exception instanceof $dev_should_report){
                    app('sentry')->captureException($exception);
                }
            }
        }
        if ($this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenExpiredException) {
            return CreateJsonResponseData::createJsonData(false,[],ApiException::TOKEN_EXPIRED,'token已失效')->setStatusCode($exception->getStatusCode());
        } else if ($exception instanceof TokenInvalidException) {
            return CreateJsonResponseData::createJsonData(false,[],ApiException::TOKEN_INVALID,'token无效')->setStatusCode($exception->getStatusCode());
        }

        if($exception instanceof ApiException){
            return CreateJsonResponseData::createJsonData(false,[],$exception->getCode(),$exception->getMessage());
        }

        if($exception instanceof ApiValidationException){
            $res_data = (array)$exception->getResponse()->getData();
            $err_msg = '';
            foreach($res_data as $msg){
                $err_msg .= $msg[0]."\n";
            }
            $err_msg = trim($err_msg,"\n");
            return CreateJsonResponseData::createJsonData(false,$res_data,$exception->getCode(), $err_msg);
        }

        if ($exception instanceof AuthenticationException) {
            return CreateJsonResponseData::createJsonData(false,[],ApiException::TOKEN_INVALID,'需登录后才能操作');
        }

        if($request->expectsJson()){
            if($exception instanceof HttpException){
                return CreateJsonResponseData::createJsonData(false,[],$exception->getStatusCode(),$exception->getMessage());
            }
            return CreateJsonResponseData::createJsonData(false,[],$exception->getCode(),'出错了,请稍后再试~');
        }

        if ($exception instanceof UnauthorizedException) {
            return redirect()
                ->route(home_route())
                ->withFlashDanger(__('auth.general_error'));
        }

        return parent::render($request, $exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException  $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthenticated.'], 401)
            : redirect()->guest(route('frontend.auth.login'));
    }
}
