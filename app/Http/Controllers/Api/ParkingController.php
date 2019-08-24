<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\ParkingData;
use App\Http\Resources\ParkingDataResource;
use Illuminate\Support\Facades\DB;
use \PDOException;

class ParkingController extends ApiController
{
    public function __construct(){
        parent::__construct();
    }

    public function list(Request $request)
    {
        $parkings = Parking::with(['user', 'device', 'location'])->get();
        $this->response_data->status    = true;
        $this->response_data->message   = 'Parking Data Retrieved';
        $this->response_data->data      = $parkings->toArray();
        return $this->json();
    }

    public function enter(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            // $request->validate([
            //     'device_key' => 'required',
            //     'merchant_id' => ['required', 'exists:users,id']
            // ]);

            $device_key = $request->device_key;
            $code = $request->code;
            if(!$parking = Parking::where('devices.key', '=', $device_key)->join('devices', 'parkings.device_id', '=', 'devices.id')->first()){
                $this->response_data->message = 'Parking Not Found';
                return $this->json();
            }
            $new_entry = ParkingData::updateOrCreate([
                'parking_id'    => $parking->id,
                'customer_id'   => $user->id,
                'status'        => ParkingData::PROCESS,
            ], [
                'code'          => $code,
                'entry_time'    => date('Y-m-d H:i:s'),
                'police_number' => 'BK 8888 INM',
                'price'         => 0
            ]);
            DB::commit();

            $this->response_data->status    = true;
            $this->response_data->message   = 'You are entering Park Area Via DigiValet';
            $this->response_data->data      = ['device_key' => $request->device_key, 'merchant_id' => $request->merchant_id, 'parking_data' => (new ParkingDataResource($new_entry))];
            return $this->json();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function confirm_enter(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $code = $request->code;
            if(!$entry = ParkingData::where('code', '=', $code)->where('customer_id', '=', $user->id)->first()){
                $this->response_data->message   = 'Cannot find Parking Data';
                $this->response_data->data = [];
                return $this->json();
            }
            $entry->entry_time = date('Y-m-d H:i:s');
            $entry->save();
            DB::commit();

            $this->response_data->status    = true;
            $this->response_data->message   = 'You entered Park Area Via DigiValet';
            $this->response_data->data      = ['parking_data' => (new ParkingDataResource($entry))];
            return $this->json();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function exit(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $s_price = 3000;
            $exit_time = new \DateTime();
            // $request->validate([
            //     'device_key' => 'required',
            //     'merchant_id' => ['required', 'exists:users,id']
            // ]);

            $device_key = $request->device_key;
            if(!$parking = Parking::where('devices.key', '=', $device_key)->join('devices', 'parkings.device_id', '=', 'devices.id')->first()){
                $this->response_data->message = 'Parking Not Found';
                return $this->json();
            }
            if(!$entry = ParkingData::where('parking_id', '=', $parking->id)->where('customer_id', '=', $user->id)->where('status', '=', ParkingData::PROCESS)->first()){
                $this->response_data->message = 'Parking Data Not Found';
                return $this->json();
            }
            $entry_time = new \DateTime($entry->entry_time);
            $time_diff = $exit_time->diff($entry_time);
            $price = $s_price * ($time_diff->h + 1);
            $entry->update([
                'exit_time'     => date('Y-m-d H:i:s'),
                'police_number' => 'BK 6305 PKI',
                'price'         => $price,
                'status'        => ParkingData::DONE,
            ]);
            DB::commit();

            $this->response_data->status    = true;
            $this->response_data->message   = 'Thank You!';
            $this->response_data->data      = ['device_key' => $request->device_key, 'merchant_id' => $request->merchant_id, 'parking_data' => $entry];
            return $this->json();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
