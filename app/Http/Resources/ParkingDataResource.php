<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParkingDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'police_number' => $this->police_number,
            'entry_time' => $this->entry_time,
            'customer' => $this->customer,
            'parking' => $this->parking,
            'status' => $this->status,
        ];
    }
}
