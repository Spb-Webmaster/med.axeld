<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\BloodPressureRequest;
use App\Models\BloodPressureReading;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Дневник давления: страница календаря и запись замеров.
 *
 * Календарь сохраняет и удаляет замеры запросами из браузера и ждёт JSON,
 * поэтому методы отвечают данными, а не редиректом.
 *
 * Дневник на сайте один — владельца (User::owner()). Смотреть его может кто
 * угодно, включая гостя; открывать страницу — любой вошедший; менять записи —
 * только сам владелец.
 */
class BloodPressureController extends Controller
{
    public function index(): View
    {
        return view('cabinet.pressure');
    }

    /**
     * Сохранение замера за день.
     *
     * Замер на день один: повторная отправка за ту же дату правит прежнюю запись.
     * Дату ищем вместе с user_id — иначе достаточно было бы подставить чужую
     * дату, чтобы переписать чужой замер.
     */
    public function store(BloodPressureRequest $request): JsonResponse
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        /*
         | Ищем именно whereDate, а не updateOrCreate по равенству: каст 'date'
         | отдаёт в запись полночь, и в SQLite колонка хранит «2026-09-26 00:00:00».
         | Сравнение с «2026-09-26» тогда не совпадает, прежний замер не находится,
         | и вставка падает на уникальном ключе. whereDate одинаково работает
         | и в MySQL, и в SQLite.
         */
        $reading = BloodPressureReading::query()
            ->forUser($userId)
            ->whereDate('measured_on', $data['date'])
            ->first()
            ?? new BloodPressureReading([
                'user_id'     => $userId,
                'measured_on' => $data['date'],
            ]);

        $reading->fill([
            'systolic'  => $data['systolic'],
            'diastolic' => $data['diastolic'],
            'pulse'     => $data['pulse'],
        ])->save();

        return response()->json([
            'saved'   => true,
            'reading' => [
                'date'  => $reading->date_key,
                'sys'   => $reading->systolic,
                'dia'   => $reading->diastolic,
                'pulse' => $reading->pulse,
            ],
        ]);
    }

    /** Удаление замера за день */
    public function destroy(Request $request, string $date): JsonResponse
    {
        // Право на запись проверяем и здесь: у удаления нет своего FormRequest
        abort_unless($request->user()->isDiaryOwner(), 403, 'Дневник ведёт только его владелец.');

        // Чужую запись не трогаем: ищем строго среди своих
        $deleted = BloodPressureReading::query()
            ->forUser($request->user()->id)
            ->whereDate('measured_on', $date)
            ->delete();

        return response()->json([
            'deleted' => $deleted > 0,
            'date'    => $date,
        ]);
    }
}
