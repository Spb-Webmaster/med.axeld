<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BloodPressureReading;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/** Выгрузка дневника давления в Excel */
class BloodPressureExportTest extends TestCase
{
    use RefreshDatabase;

    private function ownerWithReadings(): User
    {
        $owner = User::factory()->create(['published' => true]);

        foreach ([['2026-09-25', 120, 70, 68], ['2026-09-26', 130, 90, 70], ['2026-10-02', 140, 95, 75]] as $row) {
            BloodPressureReading::create([
                'user_id'     => $owner->id,
                'measured_on' => $row[0],
                'systolic'    => $row[1],
                'diastolic'   => $row[2],
                'pulse'       => $row[3],
            ]);
        }

        return $owner;
    }

    /** @return list<array<int, string|null>> */
    private function rowsOf(string $content): array
    {
        $path = tempnam(sys_get_temp_dir(), 'bp-test-');
        file_put_contents($path, $content);

        $rows = IOFactory::load($path)->getActiveSheet()->toArray(null, true, false, false);
        unlink($path);

        return $rows;
    }

    public function test_гостя_на_выгрузку_не_пускает(): void
    {
        $this->get('/cabinet/pressure/export?mode=year&year=2026')
            ->assertRedirect(route('login'));
    }

    public function test_выгрузка_за_диапазон_дат(): void
    {
        $owner = $this->ownerWithReadings();

        $response = $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=range&from=2026-09-25&to=2026-09-26')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->assertStringContainsString(
            'dnevnik-davleniya_2026-09-25_2026-09-26.xlsx',
            $response->headers->get('content-disposition')
        );

        $rows = $this->rowsOf($response->streamedContent());
        $flat = collect($rows)->map(fn ($r) => implode('|', array_map('strval', $r)))->implode("\n");

        $this->assertStringContainsString('25.09.2026|120|70|68', $flat);
        $this->assertStringContainsString('26.09.2026|130|90|70', $flat);

        // Замер за октябрь в диапазон не входит
        $this->assertStringNotContainsString('02.10.2026', $flat);
    }

    public function test_выгрузка_за_месяц_берёт_только_его_дни(): void
    {
        $owner = $this->ownerWithReadings();

        $response = $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=month&year=2026&month=10')
            ->assertOk();

        $this->assertStringContainsString(
            'dnevnik-davleniya_2026-10-01_2026-10-31.xlsx',
            $response->headers->get('content-disposition')
        );

        $flat = collect($this->rowsOf($response->streamedContent()))
            ->map(fn ($r) => implode('|', array_map('strval', $r)))->implode("\n");

        $this->assertStringContainsString('02.10.2026|140|95|75', $flat);
        $this->assertStringNotContainsString('25.09.2026', $flat);
    }

    public function test_выгрузка_за_год_берёт_все_замеры(): void
    {
        $owner = $this->ownerWithReadings();

        $response = $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=year&year=2026')
            ->assertOk();

        $flat = collect($this->rowsOf($response->streamedContent()))
            ->map(fn ($r) => implode('|', array_map('strval', $r)))->implode("\n");

        $this->assertStringContainsString('25.09.2026', $flat);
        $this->assertStringContainsString('02.10.2026', $flat);
    }

    public function test_период_без_замеров_отдаёт_файл_с_пояснением(): void
    {
        $owner = $this->ownerWithReadings();

        $response = $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=month&year=2026&month=1')
            ->assertOk();

        $flat = collect($this->rowsOf($response->streamedContent()))
            ->map(fn ($r) => implode('|', array_map('strval', $r)))->implode("\n");

        $this->assertStringContainsString('За выбранный период замеров нет', $flat);
    }

    public function test_неверный_период_отклоняется(): void
    {
        $owner = $this->ownerWithReadings();

        // Конец раньше начала
        $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=range&from=2026-09-20&to=2026-09-10')
            ->assertSessionHasErrors('to');

        // Диапазон без дат
        $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=range')
            ->assertSessionHasErrors(['from', 'to']);

        // Год раньше начала ведения дневника
        $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=year&year=2019')
            ->assertSessionHasErrors('year');

        // Неизвестный режим
        $this->actingAs($owner)
            ->get('/cabinet/pressure/export?mode=quarter')
            ->assertSessionHasErrors('mode');
    }

    public function test_выгружается_дневник_владельца_а_не_вошедшего(): void
    {
        $owner = $this->ownerWithReadings();
        $stranger = User::factory()->create(['published' => true]);

        BloodPressureReading::create([
            'user_id' => $stranger->id, 'measured_on' => '2026-09-25',
            'systolic' => 199, 'diastolic' => 99, 'pulse' => 99,
        ]);

        $response = $this->actingAs($stranger)
            ->get('/cabinet/pressure/export?mode=year&year=2026')
            ->assertOk();

        $flat = collect($this->rowsOf($response->streamedContent()))
            ->map(fn ($r) => implode('|', array_map('strval', $r)))->implode("\n");

        $this->assertStringContainsString('120|70|68', $flat);
        $this->assertStringNotContainsString('199', $flat);
    }
}
