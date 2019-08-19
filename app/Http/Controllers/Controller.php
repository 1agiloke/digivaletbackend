<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Route;
use App\Http\Helper\ResponseObject;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $response_data;

    public function __construct()
    {
        $this->response_data = new ResponseObject();
    }

    protected function view($data = [], $mergeData = [])
    {

        $currentAction = Route::getCurrentRoute()->getActionName();
        list($controller, $method) = explode('@', $currentAction);
        $controller = str_replace("App\Http\Controllers\\", "", $controller);
        $controller = explode('\\', $controller);
        $controller[count($controller) - 1] = preg_replace('/Controller$/', '', end($controller));
        $route = array_merge($controller, [$method]);

        $view = strtolower(implode($route, '.'));

        return view($view, $data, $mergeData);
    }

    protected function json($data = null, $status = 200)
    {
        $this->response_data = ($data) ?: $this->response_data;
        return response()->json($this->response_data, $status);
    }
}
