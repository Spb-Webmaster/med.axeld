<?php

declare(strict_types=1);

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Выбор периода для выгрузки дневника в Excel.
 *
 * Три режима: произвольный диапазон, целый год, отдельный месяц.
 * Границы периода считает periodFrom()/periodTo() — контроллеру остаётся
 * только передать их в BloodPressureExport.
 */
class BloodPressureExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $minYear = (int) config('site.bp_calendar.min_year', 2026);
        $maxYear = max($minYear, (int) now()->format('Y'));

        return [
            'mode' => ['required', Rule::in(['range', 'year', 'month'])],

            // Диапазон: обе даты обязательны, конец не раньше начала
            'from' => ['required_if:mode,range', 'nullable', 'date_format:Y-m-d'],
            'to'   => ['required_if:mode,range', 'nullable', 'date_format:Y-m-d', 'after_or_equal:from'],

            // Год нужен и для режима «год», и для режима «месяц»
            'year' => [
                'required_if:mode,year', 'required_if:mode,month', 'nullable',
                'integer', "min:$minYear", "max:$maxYear",
            ],

            'month' => ['required_if:mode,month', 'nullable', 'integer', 'min:1', 'max:12'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $minYear = (int) config('site.bp_calendar.min_year', 2026);

        return [
            'mode.required'     => 'Выберите, за какой период выгружать.',
            'mode.in'           => 'Выберите, за какой период выгружать.',
            'from.required_if'  => 'Укажите начало периода.',
            'from.date_format'  => 'Укажите начало периода.',
            'to.required_if'    => 'Укажите конец периода.',
            'to.date_format'    => 'Укажите конец периода.',
            'to.after_or_equal' => 'Конец периода не может быть раньше начала.',
            'year.required_if'  => 'Выберите год.',
            'year.min'          => "Дневник ведётся с $minYear года.",
            'year.max'          => 'Этот год ещё не наступил.',
            'month.required_if' => 'Выберите месяц.',
        ];
    }

    public function periodFrom(): Carbon
    {
        return match ($this->validated('mode')) {
            'year'  => Carbon::create((int) $this->validated('year'), 1, 1)->startOfDay(),
            'month' => Carbon::create((int) $this->validated('year'), (int) $this->validated('month'), 1)->startOfDay(),
            default => Carbon::createFromFormat('Y-m-d', $this->validated('from'))->startOfDay(),
        };
    }

    public function periodTo(): Carbon
    {
        return match ($this->validated('mode')) {
            'year'  => $this->periodFrom()->copy()->endOfYear()->startOfDay(),
            'month' => $this->periodFrom()->copy()->endOfMonth()->startOfDay(),
            default => Carbon::createFromFormat('Y-m-d', $this->validated('to'))->startOfDay(),
        };
    }
}
