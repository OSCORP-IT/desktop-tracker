<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAccountController extends Controller
{
    
    public function change_theme_color(Request $request) {
        $request->validate([
            'theme_color' => ['required', 'string', 'max:255'],
        ]);

        $user = User::find(Auth::user()->id);

        $user->update([
            'theme_color' => $request->theme_color
        ]);

        return back();
    }
}
