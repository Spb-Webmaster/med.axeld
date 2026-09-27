<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function setCity(Request $request): JsonResponse
    {
        $title = $request->string('title')->trim()->toString();
        // Оставляем только цифры — храним в сессии в чистом виде
        $phone = preg_replace('/\D/', '', $request->string('phone')->trim()->toString());

        session()->forget(['city_title', 'city_phone']);
        session(['city_title' => $title, 'city_phone' => $phone]);

        return response()->json([
            'city_title'           => session('city_title'),
            'city_phone'           => session('city_phone'),            // сырые цифры → href="tel:..."
            'city_phone_formatted' => format_phone(session('city_phone')), // для отображения
        ]);
    }
}
