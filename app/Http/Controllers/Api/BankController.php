<?php

namespace App\Http\Controllers\Api;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list()
    {
        $banks = Bank::get();
        $this->response_data->status    = true;
        $this->response_data->message   = 'List Bank';
        $this->response_data->data      = $banks->toArray();
        return $this->json();
    }
}
