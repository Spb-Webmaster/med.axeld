<?php

namespace App\Http\Controllers\Axios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AxiosController extends Controller
{
    public function async(Request $request)
    {
        if ($request->template === 'call_me_blue') {
            return view('axios.forms.call_me_blue');
        }

        return view('axios.forms.error.error_form');
    }

    public function callMeBlue(Request $request)
    {
        // stub — заглушка
        return response()->json(['response' => 'ok']);
    }
}
