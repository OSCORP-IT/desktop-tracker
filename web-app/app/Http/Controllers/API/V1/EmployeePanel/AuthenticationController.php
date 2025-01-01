<?php

namespace App\Http\Controllers\API\V1\EmployeePanel;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email_or_mobile_number' => ['required', 'string', 'max:255'],
            'password'  => ['required', 'string'],
        ]);

        $employee = Employee::query()
            ->where('email', $request->email_or_mobile_number)
            ->orWhere('mobile_number', $request->email_or_mobile_number)
            ->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response([
                'success'   => false,
                'message' => "Mobile number or password doesn't match!",
            ], 401);
        }

        return response([
            'success' => true,
            'message' => "Login successful",
            'result' => $employee->get_login_response(),
        ], 200);
    }
}
