<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\ConfigParking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ParkingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search;
            $start      = $request->start;
            $length     = $request->length;

            if (!empty($request->search))
                $search = $request->search['value'];
            else
                $search = null;

            $column = [
                "device_name",
                "capacity",
                "exist",
                "created_at"
            ];

            $total = DB::table('parkings')->join('users', 'parkings.user_id', '=', 'users.id')
                ->join('devices', 'parkings.device_id', '=', 'devices.id')
                ->join('locations', 'parkings.location_id', '=', 'locations.id')
                ->where('user_id', '=', Auth::user()->id)
                ->where(function ($q) use ($search) {
                    $q->where("devices.name", 'LIKE', "%$search%");
                })
                ->count();

            $data = DB::table('parkings')->join('users', 'parkings.user_id', '=', 'users.id')
                ->join('devices', 'parkings.device_id', '=', 'devices.id')
                ->join('locations', 'parkings.location_id', '=', 'locations.id')
                ->select(
                    "parkings.id AS id",
                    "parkings.capacity AS capacity",
                    "parkings.exist AS exist",
                    "devices.name AS device_name",
                    "parkings.created_at AS created_at"
                )
                ->where('user_id', '=', Auth::user()->id)
                ->where(function ($q) use ($search) {
                    $q->where("devices.name", 'LIKE', "%$search%");
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

    public function show($id)
    {
        return $this->view(['data' => Parking::find($id)]);
    }

    public function configuration(Request $request)
    {
        $validator = $request->validate([
            'open_time'     => 'required|date_format:H:i',
            'close_time'    => 'required|date_format:H:i',
            'price'         => 'required|numeric',
            'status'        => 'required|in:close,open',
        ]);

        $config = ConfigParking::where('id', $request->id)->where('day', $request->day)->first();
        $config->day = $request->day;
        $config->open_time = $request->open_time;
        $config->close_time = $request->close_time;
        $config->price = $request->price;
        $config->status = $request->status;

        if (!$config->save()) {
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
