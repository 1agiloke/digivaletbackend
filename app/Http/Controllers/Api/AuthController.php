<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Propaganistas\LaravelPhone\PhoneNumber;
use JWTAuth;

class AuthController extends ApiController
{
    public function __construct(){
        parent::__construct();
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email'=>['required', 'unique:customers,email'],
            'name'=>'required',
            'phone'=>'required',
            'password'=>['required', 'confirmed'],
        ]);
        if($customer = $this->create($request->toArray()) ){
            return $this->login($request);
        } else{
            $this->code = 200;
            $this->response_data->status = false;
            $this->response_data->message = __('auth.failed');

            return $this->json($this->response_data, $this->code);
        }
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required',
            'password'     => 'required',
//            'push_notification'     => 'required',
//            'device'     => 'required',
        ]);

        $email = $request->email;

        $credentials = array (
            'email' => $email,
            'password'  => $request->password,
        );

        if ($token = $this->guard()->attempt($credentials)) {
            /** Send Logout Signal to another device **/
            $payload = $this->guard()->getPayload($token)->toArray();
            if($this->guard()->user()->status == Customer::NONACTIVE){
                $this->code = 200;
                $this->response_data->status = false;
                $this->response_data->message = __('auth.nonactive');
                return $this->json($this->response_data, $this->code);
            }
            /** Update Firebase Token **/
             Customer::where('email', $email)
                 ->update([
                     'device'            => $request->device,
                     'push_notification' => $request->push_notification
                 ]);
            return $this->respondWithToken($token);
        }

        $this->code = 200;
        $this->response_data->status = false;
        $this->response_data->message = __('auth.failed');

        return $this->json($this->response_data, $this->code);
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = $this->guard()->user();
        // $user->firebase = null;
        $user->save();

        $this->guard()->logout();

        $this->response = [
            'success' => true,
            'message' => 'Successfully logged out',
        ];
        return response()->json($this->response);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    public function me()
    {
        $this->response_data->status    = true;
        $this->response_data->data      = new CustomerResource($this->guard()->user());
        return $this->json();
    }

    /**
     * Reset Password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $token = $request->token;

        if ($this->verifyFirebaseToken($token)) {

            $validator = Validator::make($request->all(),
                [
                    'phone'     => 'required|phone:ID',
                    'password'  => 'required|string|min:6|max:191|confirmed',
                ]
            );

            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->getMessages() as $field => $message) {
                    $errors[] = [
                        'field' => $field,
                        'message' => $message[0],
                    ];
                }

                $this->code = 422;
                $this->response->success = false;
                $this->response->error = $errors;
            } else {
                $username = PhoneNumber::make($request->phone, 'ID')->formatE164();
                $user = Customer::where('phone', $username)->first();

                if(!$user){
                    $this->code = 400;
                    $this->response->success = false;
                    $this->response->message = "Phone Invalid.";
                    return response()->json($this->response, $this->code);
                }

                // All checking method passed, then attempt to reset the password
                // hashing the new password
                $user->password = Hash::make($request->password);

                if ($user->save()) {
                    $this->response->message = __('passwords.reset');
                } else {
                    $this->code = 500;
                    $this->response->success = false;
                }
            }

        } else {
            $this->code = 400;
            $this->response->success = false;
            $this->response->message = "Token Invalid.";
        }

        return response()->json($this->response, $this->code);
    }

    /**
     * Change Password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'old_password'  => 'required|string|min:6|max:191',
                'new_password'  => 'required|string|min:6|max:191|confirmed',
            ]
        );

        if ($validator->fails()) {

            $errors = [];
            foreach ($validator->errors()->getMessages() as $field => $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message[0],
                ];
            }

            $this->code = 422;
            $this->response->success = false;
            $this->response->error = $errors;

        } else {
            $user = $this->guard()->user();

            // check for password
            $credentials = [
                'phone'    => $user->phone,
                'password' => $request->old_password,
            ];

            if ($token = $this->guard()->attempt($credentials)) {
                // password valid
                $user->password = Hash::make($request->new_password);
                $user->save();
                $this->response->message = __('passwords.changed');
            } else {
                $this->code = 401;
                $this->response->success = false;
                $this->response->message = __('auth.wrong_password');
            }
        }

        return response()->json($this->response, $this->code);
    }

    public function phoneNumberVerify($phone)
    {
        $validator = Validator::make(['phone' => $phone],
            [
                'phone'     => 'required|phone:ID',
            ]
        );

        if ($validator->fails()) {
            $this->code = 400;
            $this->response->success = false;
            $this->response->message = __('validation.phone', ['attribute' => 'phone']);
            return response()->json($this->response, $this->code);
        }

        if (!Customer::where('phone', PhoneNumber::make($phone, 'ID')->formatE164())->first()) {
            $this->code = 400;
            $this->response->success = false;
            $this->response->message = __('validation.exists', ['attribute' => 'phone']);
        }

        return response()->json($this->response, $this->code);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithToken($token)
    {
        $this->response_data->data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => new CustomerResource($this->guard()->user())
        ];

        $this->response_data->status = true;

        return $this->json($this->response_data);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Member
     */
    private function create(array $data)
    {
        return Customer::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'push_notification' => empty($data['push_notification'])?null:$data['push_notification'],
            'saldo' => 0
        ]);
    }

    /**
     * Verify ID Firebase Token
     *
     * @param  string  $token
     * @return boolean
     */
    private function verifyFirebaseToken($token) {
        // uncomment code below to pass this validation for debug purpose
        // return true;

        $verified = false;
        $projectID = config('services.firebase.projectid');

        $token  = (new Parser())->parse((string) $token);
        $header = $token->getHeaders();
        $kid    = $header['kid'];

        $data = new ValidationData();
        $data->setIssuer('https://securetoken.google.com/'.$projectID);
        $data->setAudience($projectID);
        if(!$token->validate($data)){
            return false;
        }

        $securetoken = $this->getFirebasePublicKey();
        $securetoken = json_decode($securetoken);

        $signer = new Sha256();
        $keychain = new Keychain();

        $verified = $token->verify($signer, $securetoken->$kid);

        return $verified;
    }

    /**
     * Get Firebase Public Key to verify the signature
     *
     * @param  void
     * @return mixed
     */
    private function getFirebasePublicKey() {
        $url = "https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
