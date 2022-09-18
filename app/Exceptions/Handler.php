<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception->getMessage() == 'Unauthenticated.'){
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.', 'data' => trans('response.422')
            ], 422);
        }
        if($exception->getMessage() == 'Invalid scope(s) provided.'){
            return response()->json([
                'success' => false,
                'message' => trans('response.permission_error'), 'data' => trans('response.403')
            ], 403);
        }
        if ($request->wantsJson()) {   //add Accept: application/json in request
            return $this->handleApiException($request, $exception);
        } else {
            $retval = parent::render($request, $exception);
        }
        return $retval;
    }

    private function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);
        if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
            $exception = $exception->getResponse();
        }
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
            return response()->json(json_decode($exception->getContent()), $exception->getStatusCode());
        }
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return response()->json([
                'success' => false,
                'message' => trans('response.permission_error'), 'data' => trans('response.403')
            ], 403);
        }
        return $this->customApiResponse($exception);
    }

    private function customApiResponse($exception)
    {

        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }
        $response = [];

        switch ($statusCode) {
            case 400:
                $response['message'] = trans('response.400');
                break;
            case 401:
                $response['message'] = trans('response.401');
                break;
            case 403:
                $response['message'] = trans('response.403');
                break;
            case 404:
                $response['message'] = trans('response.404');
                break;
            case 405:
                $response['message'] = trans('response.405');
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $response['message'] = ($statusCode == 500) ? trans('response.500') : $exception->getMessage();
                break;
        }

        if (config('app.debug')) {
            //            $response['trace'] = $exception->getTrace();
            $response['error'] = $exception->getMessage();
            $response['line'] = $exception->getLine();
            $response['code'] = $exception->getCode();
            $response['file'] = $exception->getFile();
        }
        $response['status'] = $statusCode;
        return response()->json($response, $statusCode);
    }
}
