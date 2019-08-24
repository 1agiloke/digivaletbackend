<?php

namespace App\Exceptions;

use App\Http\Helper\ResponseObject;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report($exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, $exception)
    {
        if ($request->segment(1) === 'api'){
            if ($exception instanceof UnauthorizedHttpException) {
                if($exception->getPrevious()){
                    switch (get_class($exception->getPrevious())) {
                        case TokenExpiredException::class:
                            return response()->json([
                                'status' => false,
                                'message' => 'Token has expired. Please logout and login again',
                                'code' => 401
                            ], $exception->getStatusCode());
                        case TokenInvalidException::class:
                            return response()->json([
                                'status' => false,
                                'message' => 'Token is invalid. Please logout and login again',
                                'code' => 401
                            ], $exception->getStatusCode());
                        case TokenBlacklistedException::class:
                            return response()->json([
                                'status' => false,
                                'message' => 'Token is invalid. Please logout and login again',
                                'code' => 401
                            ], $exception->getStatusCode());
                        case JWTException::class:
                            return response()->json([
                                'status' => false,
                                'message' => 'Token not provided',
                                'code' => 401
                            ], $exception->getStatusCode());
                        default:
                            return response()->json([
                                'status' => false,
                                'message' => $exception->getMessage(),
                                'code' => 401
                            ], $exception->getStatusCode());
                    }
                } else{
                    return response()->json([
                        'status' => false,
                        'message' => $exception->getMessage(),
                        'code' => 401
                    ], $exception->getStatusCode());
                }

            } else if($exception instanceof NotFoundHttpException) {
                if($exception->getPrevious()){
                    switch (get_class($exception->getPrevious())) {
                        default:
                            return response()->json([
                                'status' => false,
                                'message' => "request not found",
                                'code' => 404
                            ], $exception->getStatusCode());
                    }
                } else{
                    return response()->json([
                        'status' => false,
                        'message' => "request not found",
                        'code' => 404
                    ], $exception->getStatusCode());
                }
            }else if($exception instanceof MethodNotAllowedHttpException){
                return response()->json([
                    'status' => false,
                    'message' => 'Method Not Allowed',
                ], $exception->getStatusCode());
            } else if($exception instanceof AuthenticationException){
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage(),
                    'code' => 401
                ], 401);
            }
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert a validation exception into a JSON response.
     * Special case for request from API route
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        if (!($response = $this->invalidApi($request, $exception)))
            return parent::invalidJson($request, $exception); // TODO: Change the autogenerated stub
        return $response;
    }

    /**
     * Convert a validation exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function invalid($request, ValidationException $exception)
    {
        if (!($response = $this->invalidApi($request, $exception)))
            return parent::invalid($request, $exception); // TODO: Change the autogenerated stub

        return $response;
    }

    /**
     * Convert a validation exception into a JSON response.
     * Special case for request from API route
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse|boolean
     */
    private function invalidApi($request, ValidationException $exception){
        if (in_array('api', $request->route()->action['middleware'])){
            $errors = [];
            foreach ($exception->errors() as $field => $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message[0],
                ];
            }

            $response = new ResponseObject();
            $response->errors = $errors;

            return response()->json($response, $exception->status);
        }else{
            return false;
        }
    }
}
