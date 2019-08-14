<?php

namespace App\Http\Controllers;

use App\Models\ParkingData;
use Illuminate\Http\Request;

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
                "police_number",
                "date",
                "day",
                "time_in",
                "time_out",
                "price",
                "status"
            ];

            $total = ParkingData::where("police_number", 'LIKE', "%$search%")
                ->where("status", 'LIKE', "%$status%")
                ->count();

            $data = ParkingData::where("police_number", 'LIKE', "%$search%")
                ->where("status", 'LIKE', "%$status%")
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
