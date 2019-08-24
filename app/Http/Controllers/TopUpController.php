<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TopUpController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search;
            $start = $request->start;
            $length = $request->length;

            if (!empty($request->search))
                $search = $request->search['value'];
            else
                $search = null;

            $column = [
                "nominal",
                "bank",
                "status",
                "created_at"
            ];

            $total = Deposit::with(['bank' => function ($q) use ($search) {
                    $q->where("name", 'LIKE', "%$search%");
                }])
                ->where('user_id', '=', Auth::user()->id)
                ->where(function ($q) use ($search) {
                    $q->where("nominal", 'LIKE', "%$search%")
                    ->orWhere("created_at", 'LIKE', "%$search%");
                })
                ->count();

            $data = Deposit::with(['bank' => function ($q) use ($search) {
                    $q->where("name", 'LIKE', "%$search%");
                }])
                ->where('user_id', '=', Auth::user()->id)
                ->where(function ($q) use ($search) {
                    $q->where("nominal", 'LIKE', "%$search%")
                    ->orWhere("created_at", 'LIKE', "%$search%");
                })
                ->orderBy($column[$request->order[0]['column'] - 1], $request->order[0]['dir'])
                ->skip($start)
                ->take($length)
                ->get();

            $response = [
                'data' => $data,
                'draw' => intval($request->draw),
                'recordsTotal' => $total,
                'recordsFiltered' => $total
            ];

            return response()->json($response);
        }
        return $this->view();
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $request->validate([
                'bank'      => 'required',
                'nominal'   => 'required|numeric',
            ]);

            $statusRes = false;
            $id_deposit= "";

            DB::transaction(function () use ($request, &$statusRes, &$id_deposit) {
                $deposit = new Deposit();
                $deposit->nominal = $request->nominal;
                $deposit->unique_code = rand(111, 999);
                $deposit->status = 'success';
                $deposit->bank_id = $request->bank;
                $deposit->user_id = Auth::user()->id;

                if ($deposit->save()) {
                    $user = User::find(Auth::user()->id);
                    $user->saldo = intval($user->saldo) + intval($request->nominal);
                    $user->save();

                    $statusRes = true;
                    $id_deposit = $deposit->id;
                }
            });

            if (!$statusRes) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Failed to Top Up'
                ]);
            } else {
                return response()->json([
                    'success'   => true,
                    'id'        => $id_deposit,
                ]);
            }
        }

        return $this->view([
            'banks' => Bank::get(),
        ]);
    }

    public function transfer($id)
    {
        return $this->view([
            'data' => Deposit::find($id)
        ]);
    }
}
