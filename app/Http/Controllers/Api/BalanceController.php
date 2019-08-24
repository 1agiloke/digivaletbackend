<?php

namespace App\Http\Controllers\Api;

use App\Models\Deposit;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BalanceController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list(Request $request)
    {
        $deposit = Deposit::with(['bank', 'customer'])->where('customer_id', $request->user()->id)->get();
        $this->response_data->status    = true;
        $this->response_data->message   = 'List Balance';
        $this->response_data->data      = $deposit->toArray();
        return $this->json();
    }

    public function topUp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nominal'   => 'required|numeric',
                'bank'      => 'required',
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
            $this->response->status = false;
            $this->response->error = $errors;
        } else {
            $statusRes  = false;
            $id_deposit = "";

            DB::transaction(function () use ($request, &$statusRes, &$id_deposit) {
                $deposit                = new Deposit();
                $deposit->nominal       = $request->nominal;
                $deposit->unique_code   = rand(111, 999);
                $deposit->status        = 'success';
                $deposit->bank_id       = $request->bank;
                $deposit->customer_id   = $request->user()->id;

                if($deposit->save()){
                    $customer = Customer::find($request->user()->id);
                    $customer->saldo = intval($customer->saldo) + intval($request->nominal);
                    $customer->save();

                    $statusRes = true;
                    $id_deposit = $deposit->id;
                }
            });

            $deposit = Deposit::with(['bank'])->find($id_deposit);

            $this->response->status    = true;
            $this->response->message   = 'Top Up Balance Successfully';
            $this->response->data      = $deposit;
        }

        return response()->json($this->response, $this->code);

    }
}
