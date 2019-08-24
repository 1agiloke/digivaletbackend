<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LocationController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function near(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'latitude'  => 'required',
                'longitude' => 'required',
                'radius'    => 'nullable'
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
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius != null ? $request->radius : 0;

            $data = DB::table('locations')
                ->join('parkings', 'locations.id', '=', 'parkings.location_id')
                ->join('users', 'parkings.user_id', '=', 'users.id')
                ->join('devices', 'parkings.device_id', '=', 'devices.id')
                ->join('config_parkings', 'parkings.id', '=', 'config_parkings.parking_id')
                ->selectRaw("
                    (locations.latitude * radians(1)) AS lat1,
                    (locations.longitude * radians(1)) AS lng1,
                    ({$lat} * radians(1)) AS lat2,
                    ({$lng} * radians(1)) AS lng2,
                    (({$lng} * radians(1)) - (locations.longitude * radians(1))) * cos(((locations.latitude * radians(1)) + ({$lat} * radians(1)))/2) AS tempX,
                    ({$lat} * radians(1)) - (locations.latitude * radians(1)) AS tempY,
                    SQRT( ( ((({$lng} * radians(1)) - (locations.longitude * radians(1))) * cos(((locations.latitude * radians(1)) + ({$lat} * radians(1)))/2)) * ((({$lng} * radians(1)) - (locations.longitude * radians(1))) * cos(((locations.latitude * radians(1)) + ({$lat} * radians(1)))/2)) ) + ( (({$lat} * radians(1)) - (locations.latitude * radians(1))) * (({$lat} * radians(1)) - (locations.latitude * radians(1))) ) ) * 6371 AS distance,
                    users.name AS owner_name,
                    parkings.id AS parkings_id,
                    devices.name AS device_name,
                    locations.address AS parkings_address,
                    config_parkings.day AS parkings_day,
                    config_parkings.open_time AS parkings_open_time,
                    config_parkings.close_time AS parkings_close_time,
                    config_parkings.price AS parkings_price,
                    config_parkings.status AS parkings_status

                ")
                ->having('distance', '<', $radius)
                ->where('config_parkings.day', '=', date("w"))
                ->orderBy('distance', 'asc')
                ->limit(5)
                ->get();

            $this->code = 200;
            $this->response->status    = true;
            $this->response->message   = 'Location Near';
            $this->response->data      = $data;

        }

        return response()->json($this->response, $this->code);
    }
}
