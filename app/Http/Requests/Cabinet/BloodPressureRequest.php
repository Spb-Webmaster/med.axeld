<?php

declare(strict_types=1);

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Замер давления и пульса за конкретный день.
 *
 * Дата приходит из календаря в виде ГГГГ-ММ-ДД. Будущее отсекаем:
 * отметить можно сегодняшний день и любой прошедший, завтрашний — нет.
 *
 * Дневник на сайте один — владельца. Просмотр открыт всем, запись только ему.
 */
class BloodPressureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isDiaryOwner() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $limits = config('site.bp_calendar.limits');
        $minYear = (int) config('site.bp_calendar.min_year', 2026);

        return [
            'date' => [
                'required', 'date_format:Y-m-d',
                'after_or_equal:' . $minYear . '-01-01',
                'before_or_equal:today',
            ],

            /*
             | integer + min/max, а не просто numeric: три поля календаря — целые.
             | gt:diastolic у верхнего — верхнее давление всегда больше нижнего,
             | перепутанные местами значения покрасили бы ячейку не тем цветом.
             */
            'diastolic' => ['required', 'integer', "min:{$limits['diastolic']['min']}", "max:{$limits['diastolic']['max']}"],
            'systolic'  => ['required', 'integer', "min:{$limits['systolic']['min']}", "max:{$limits['systolic']['max']}", 'gt:diastolic'],
            'pulse'     => ['required', 'integer', "min:{$limits['pulse']['min']}", "max:{$limits['pulse']['max']}"],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $limits = config('site.bp_calendar.limits');
        $minYear = (int) config('site.bp_calendar.min_year', 2026);

        return [
            'date.required'        => 'Не указана дата замера.',
            'date.date_format'     => 'Не указана дата замера.',
            'date.after_or_equal'  => "Дневник ведётся с $minYear года.",
            'date.before_or_equal' => 'Записать замер можно только за сегодня или за прошедший день.',

            'systolic.required' => 'Введите верхнее давление.',
            'systolic.integer'  => 'Верхнее давление — целое число.',
            'systolic.min'      => "Верхнее давление — от {$limits['systolic']['min']} до {$limits['systolic']['max']}.",
            'systolic.max'      => "Верхнее давление — от {$limits['systolic']['min']} до {$limits['systolic']['max']}.",
            'systolic.gt'       => 'Верхнее давление должно быть больше нижнего.',

            'diastolic.required' => 'Введите нижнее давление.',
            'diastolic.integer'  => 'Нижнее давление — целое число.',
            'diastolic.min'      => "Нижнее давление — от {$limits['diastolic']['min']} до {$limits['diastolic']['max']}.",
            'diastolic.max'      => "Нижнее давление — от {$limits['diastolic']['min']} до {$limits['diastolic']['max']}.",

            'pulse.required' => 'Введите пульс.',
            'pulse.integer'  => 'Пульс — целое число.',
            'pulse.min'      => "Пульс — от {$limits['pulse']['min']} до {$limits['pulse']['max']}.",
            'pulse.max'      => "Пульс — от {$limits['pulse']['min']} до {$limits['pulse']['max']}.",
        ];
    }
}
