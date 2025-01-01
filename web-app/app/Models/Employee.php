<?php

namespace App\Models;

use App\Http\Resources\EmployeeResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable {
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function get_login_response($token = null, $additional = []) {
        EmployeeResource::withoutWrapping();

        if ($token === null) {
            $token = $this->login_and_get_token();
        }

        return [
            'client' => (new EmployeeResource($this)),
            'token' => $token,
            'token_hash' => base64_encode($token),
        ] + $additional;
    }

    public function login_and_get_token() {
        return $this->createToken(request()->ip())->plainTextToken;
    }
}
