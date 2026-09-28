<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\BloodPressureReading;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Выгрузка дневника давления в .xlsx за выбранный период.
 *
 * Даты пишутся датами, а числа числами (а не строками): иначе Excel не даст
 * ни отсортировать выгрузку, ни построить по ней график.
 */
class BloodPressureExport
{
    /** Ширина колонок — под самый длинный заголовок, чтобы ничего не пряталось */
    private const COLUMNS = [
        'A' => ['Дата', 14],
        'B' => ['Верхнее', 11],
        'C' => ['Нижнее', 11],
        'D' => ['Пульс', 11],
    ];

    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly int $userId,
    ) {
    }

    /** Имя файла — латиницей с границами периода: так его проще найти в загрузках */
    public function fileName(): string
    {
        return sprintf(
            'dnevnik-davleniya_%s_%s.xlsx',
            $this->from->format('Y-m-d'),
            $this->to->format('Y-m-d')
        );
    }

    /** Готовый файл во временной папке; вызывающий отдаёт его через download() */
    public function toTempFile(): string
    {
        $spreadsheet = $this->build();

        $path = tempnam(sys_get_temp_dir(), 'bp-export-');
        (new Xlsx($spreadsheet))->save($path);

        // Лист держит в памяти всю сетку, а запросов на выгрузку может быть много подряд
        $spreadsheet->disconnectWorksheets();

        return $path;
    }

    private function readings(): Collection
    {
        return BloodPressureReading::query()
            ->forUser($this->userId)
            ->whereDate('measured_on', '>=', $this->from->format('Y-m-d'))
            ->whereDate('measured_on', '<=', $this->to->format('Y-m-d'))
            ->orderBy('measured_on')
            ->get();
    }

    private function build(): Spreadsheet
    {
        $readings = $this->readings();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Дневник давления');

        // Шапка с периодом — по ней понятно, за что выгрузка, даже если файл переименуют
        $sheet->setCellValue('A1', sprintf(
            'Дневник давления: %s — %s',
            $this->from->format('d.m.Y'),
            $this->to->format('d.m.Y')
        ));
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $row = 3;
        foreach (self::COLUMNS as $column => [$title, $width]) {
            $sheet->setCellValue($column . $row, $title);
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:D$row")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EAF1F7');

        $firstDataRow = $row + 1;

        foreach ($readings as $reading) {
            $row++;

            /*
             | Дату кладём строкой d.m.Y, а не числом Excel: пользователь открывает
             | файл в разных программах, и «сырая» дата 46000 без формата выглядит
             | как ошибка. Порядок строк задан сортировкой запроса.
             */
            $sheet->setCellValue('A' . $row, $reading->measured_on->format('d.m.Y'));
            $sheet->setCellValue('B' . $row, $reading->systolic);
            $sheet->setCellValue('C' . $row, $reading->diastolic);
            $sheet->setCellValue('D' . $row, $reading->pulse);
        }

        if ($readings->isEmpty()) {
            $row++;
            $sheet->setCellValue('A' . $row, 'За выбранный период замеров нет');
            $sheet->mergeCells("A$row:D$row");
        } else {
            $lastDataRow = $row;

            // Средние по периоду: их обычно и спрашивает врач
            $row += 2;
            $sheet->setCellValue('A' . $row, 'Среднее');
            $sheet->setCellValue('B' . $row, "=ROUND(AVERAGE(B$firstDataRow:B$lastDataRow),0)");
            $sheet->setCellValue('C' . $row, "=ROUND(AVERAGE(C$firstDataRow:C$lastDataRow),0)");
            $sheet->setCellValue('D' . $row, "=ROUND(AVERAGE(D$firstDataRow:D$lastDataRow),0)");
            $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);

            $sheet->getStyle("A$firstDataRow:D$lastDataRow")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle("B3:D$row")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Шапка таблицы остаётся на виду при прокрутке длинной выгрузки
        $sheet->freezePane('A4');

        return $spreadsheet;
    }
}
