<?php

namespace App\Http\Helper;


class ResponseObject
{
    public $status;
    public $data;
    public $message;
    public $errors;

    function __construct()
    {
        $this->status = false;
        $this->data = new \stdClass();
        $this->message = "";
        $this->errors = [];
    }
}