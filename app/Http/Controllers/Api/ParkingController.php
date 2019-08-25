<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\ParkingData;
use App\Models\ConfigParking;
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

    public function current(Request $request)
    {
        $user = $request->user();
        if(!$current_parking = ParkingData::where('customer_id', '=', $user->id)->where('status', '=', ParkingData::PROCESS)->first()){
            $this->response_data->message   = 'There is no currently processing parking data';
            return $this->json();
        }
        $this->response_data->status    = true;
        $this->response_data->message   = 'Parking Data Retrieved';
        $this->response_data->data      = (new ParkingDataResource($current_parking));
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
            $s_price = 1000;
            $exit_time = new \DateTime();
            // $request->validate([
            //     'device_key' => 'required',
            //     'merchant_id' => ['required', 'exists:users,id']
            // ]);

            $parking_data_id = $request->parking_data_id;
            if(!$entry = ParkingData::where('id', '=', $parking_data_id)->where('customer_id', '=', $user->id)->first()){
                $this->response_data->message = 'Parking Data Not Found';
                return $this->json();
            }
            $entry_time = new \DateTime($entry->entry_time);
            $time_diff = $exit_time->diff($entry_time);
            if($config = ConfigParking::where('day', '=', date('w', strtotime($entry->entry_time) ))->where('parking_id', '=', $entry->parking_id)->first()){
                $s_price = $config->price;
            }
            $price = $s_price * ($time_diff->h + 1);
            $entry->update([
                'exit_time'     => date('Y-m-d H:i:s'),
                'price'         => $price,
                'status'        => ParkingData::DONE,
            ]);
            $user->saldo  -= $price;
            $user->save();
            DB::commit();
            $this->notify_via_bigbox($request);
            $this->response_data->status    = true;
            $this->response_data->message   = 'Thank You!';
            $this->response_data->data      = ['device_key' => $request->device_key, 'merchant_id' => $request->merchant_id, 'parking_data' => $entry];
            return $this->json();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    private function notify_via_bigbox($price){
        $postData = array(
            'msisdn' => '085261538606',
            'content' => 'Your exited from DigiValet. Your balance of ' . $price . ' has been successfully deducted.',
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.thebigbox.id/sms-notification/1.0.0/messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/x-www-form-urlencoded",
                "Content-Type: application/x-www-form-urlencoded",
                "x-api-key: 33ti4mLfTbe7mhw5HU9YO8TI3XqGOQ6Z"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    }
}
