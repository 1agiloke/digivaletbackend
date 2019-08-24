<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
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
                "created_at"
            ];

            $total = User::where("name", 'LIKE', "%$search%")
                ->orWhere("email", 'LIKE', "%$search%")
                ->orWhere("phone", 'LIKE', "%$search%")
                ->count();

            $data = User::where("name", 'LIKE', "%$search%")
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

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name'      => 'required|string|max:191',
            'email'     => 'required|email|unique:users',
            'phone'     => 'required|string|unique:users|phone:ID',
        ]);

        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->phone    = $request->phone;
        $user->password = Hash::make('123456');

        if (!$user->save()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Failed to Add'
            ]);
        } else {
            return response()->json([
                'success'  => true,
                'message'  => 'Added Successfully'
            ]);
        }
    }
}
