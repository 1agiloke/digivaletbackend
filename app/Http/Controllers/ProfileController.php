<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        return $this->view([
            'data' => User::find(Auth::user()->id),
        ]);
    }

    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!(Hash::check($request->current_password, $user->password))) {
            return response()->json([
                'success' => false,
                'message' => 'Current Password Wrong, Please Try Again.',
            ]);
        }

        $validator = $request->validate([
            'new_password'         => 'required|min:6',
            'new_password_confirm' => 'required_with:new_password|same:new_password|min:6',
        ]);

        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Password Successfully changed',
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Password Failed to change',
            ]);
        }
    }

    public function changeSetting(Request $request, $id)
    {
        $validator = $request->validate([
            'name'          => 'nullable|string|max:191',
            'phone'         => ['nullable', 'string', Rule::unique('users')->ignore($id)],
        ]);

        $user = User::find($id);
        $user->name         = $request->name;
        $user->phone        = $request->phone;

        if ($user->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Merubah',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Merubah',
            ]);
        }
    }
}
