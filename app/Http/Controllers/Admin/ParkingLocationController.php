<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Device;
use App\Models\Parking;
use App\Models\Location;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Models\ConfigParking;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ParkingLocationController extends Controller
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
                "name",
                "capacity",
                "exist",
                "created_at"
            ];

            $total = DB::table('parkings')->join('users', 'parkings.user_id', '=', 'users.id')
                ->join('devices', 'parkings.device_id', '=', 'devices.id')
                ->join('locations', 'parkings.location_id', '=', 'locations.id')
                ->where("users.name", 'LIKE', "%$search%")
                ->orWhere("devices.name", 'LIKE', "%$search%")
                ->count();

            $data = DB::table('parkings')->join('users', 'parkings.user_id', '=', 'users.id')
                ->join('devices', 'parkings.device_id', '=', 'devices.id')
                ->join('locations', 'parkings.location_id', '=', 'locations.id')
                ->select(
                    "parkings.id AS id",
                    "parkings.capacity AS capacity",
                    "parkings.exist AS exist",
                    "users.name AS name",
                    "devices.name AS device_name",
                    "parkings.created_at AS created_at"
                )
                ->where("users.name", 'LIKE', "%$search%")
                ->orWhere("devices.name", 'LIKE', "%$search%")
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
                'merchant'  => 'required|numeric',
                'capacity'  => 'required|numeric',
                'title'     => 'required|string',
                'latitude'  => 'required',
                'longitude' => 'required',
            ]);

            $statusRes = false;

            DB::transaction(function () use ($request, &$statusRes) {
                $merchant = User::find($request->merchant);

                $dataReq = [
                    'merchan_name'    => date("YmdHis"),
                ];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://ae510381.ngrok.io/antares/create_device",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($dataReq),
                    CURLOPT_HTTPHEADER => array(
                        "Accept: */*",
                        "Content-Type: application/json",
                    ),
                ));

                $response   = curl_exec($curl);
                $err        = curl_error($curl);
                $res        = json_decode($response, true);
                $dataRes    = $res['data'];

                curl_close($curl);

                $device         = new Device();
                $device->key    = $dataRes['key'];
                $device->name   = $dataRes['merchan_name'];
                $device->save();

                $location               = new Location();
                $location->address      = $request->title;
                $location->longitude    = $request->longitude;
                $location->latitude     = $request->latitude;
                $location->save();

                $parking                = new Parking();
                $parking->capacity      = $request->capacity;
                $parking->location_id   = $location->id;
                $parking->device_id     = $device->id;
                $parking->user_id       = $merchant->id;


                if (!$parking->save()) {
                    $statusRes = false;
                } else {
                    for ($i=0; $i < 7; $i++) {
                        $configParking              = new ConfigParking();
                        $configParking->parking_id  = $parking->id;
                        $configParking->day         = strval($i);
                        $configParking->save();
                    }

                    $statusRes = true;
                }
            });

            if (!$statusRes) {
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

        return $this->view([
            'merchants' => User::get(),
        ]);
    }
}
