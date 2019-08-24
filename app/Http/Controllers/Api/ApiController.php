<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class ApiController extends Controller
{
    public $code;
    public $response;

	/**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);

        $this->response = new \stdClass();
        $this->response->status = true;
        $this->response->error = [];
        $this->response->data = [];
        $this->response->message = '';
        $this->code = 200;
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return boolean
     */
    public function check_validation($validator){
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $field => $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message[0],
                ];
            }

            $this->code = 422;
            $this->response_data->errors = $errors;
        }

        return $validator->fails();
    }
}
