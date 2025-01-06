<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function index() {
        return redirect()
            ->to('admin-panel/dashboard');
    }
}
