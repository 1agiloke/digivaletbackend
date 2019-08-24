<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
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
                "name",
                "email",
                "phone",
                "saldo",
                "status",
                "created_at"
            ];

            $total = Customer::where("name", 'LIKE', "%$search%")
                ->orWhere("email", 'LIKE', "%$search%")
                ->orWhere("phone", 'LIKE', "%$search%")
                ->count();

            $data = Customer::where("name", 'LIKE', "%$search%")
                ->orWhere("email", 'LIKE', "%$search%")
                ->orWhere("phone", 'LIKE', "%$search%")
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

    public function changeStatus(Request $request)
    {
        $validator = $request->validate([
            'status'      => 'required|in:active,non-active',
        ]);

        $customer = Customer::find($request->id);
        $customer->status = $request->status;

        if (!$customer->save()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Failed to Change Status'
            ]);
        } else {
            return response()->json([
                'success'  => true,
                'message'  => 'Change Status Successfully'
            ]);
        }
    }
}
