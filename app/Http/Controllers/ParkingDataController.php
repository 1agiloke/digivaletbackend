<?php

namespace App\Http\Controllers;

use App\Models\ParkingData;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParkingDataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search;
            $start  = $request->start;
            $length = $request->length;
            $status = $request->status;

            if (!empty($request->search))
                $search = $request->search['value'];
            else
                $search = null;

            $column = [
                "code",
                "police_number",
                "entry_time",
                "exit_time",
                "price",
            ];

            $getParking = [];
            $parkings = Parking::where('user_id', Auth::user()->id)->get();
            foreach ($parkings as $parking) {
                array_push($getParking, $parking->id);
            }

            $total = ParkingData::with(['customer', 'parking' => function($q){
                        $q->with(['config_parkings']);
                }])
                ->whereIn('parking_id', $getParking)
                ->where("status", 'LIKE', "%$status%")
                ->where( function($q) use ($search) {
                    $q->where("police_number", 'LIKE', "%$search%")
                    ->orWhere("code", 'LIKE', "%$search%");
                })
                ->count();

            $data = ParkingData::with(['customer', 'parking' => function ($q) {
                    $q->with(['config_parkings']);
                }])
                ->whereIn('parking_id', $getParking)
                ->where("status", 'LIKE', "%$status%")
                ->where( function($q) use ($search) {
                    $q->where("police_number", 'LIKE', "%$search%")
                    ->orWhere("code", 'LIKE', "%$search%");
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
}
