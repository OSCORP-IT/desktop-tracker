<?php

namespace App\Http\Controllers\API\V1\EmployeePanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        return response([
            'success' => true,
            'message' => "Done !!!"
        ], 200);
    }
}
