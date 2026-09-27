<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index():View
    {
        $home = Setting::getGroup('home')->data;


        if(auth()->check()) {
            $user = auth()->user();
        } else {
            $user = false;
        }



        return view('home', [
                'user' => $user,
                'home' => $home,
            ]
        );
    }
}
