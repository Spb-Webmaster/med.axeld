<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BloodPressureReading;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Запись замеров давления в календаре */
class BloodPressureTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['published' => true]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'date'      => now()->format('Y-m-d'),
            'systolic'  => 120,
            'diastolic' => 80,
            'pulse'     => 72,
        ], $overrides);
    }

    public function test_гость_не_может_записать_замер(): void
    {
        $this->post('/cabinet/pressure', $this->payload())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('blood_pressure_readings', 0);
    }

    public function test_замер_за_сегодня_сохраняется(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->postJson('/cabinet/pressure', $this->payload())
            ->assertOk()
            ->assertJson(['saved' => true, 'reading' => ['sys' => 120, 'dia' => 80, 'pulse' => 72]]);

        $this->assertDatabaseHas('blood_pressure_readings', [
            'user_id'   => $user->id,
            'systolic'  => 120,
            'diastolic' => 80,
            'pulse'     => 72,
        ]);
    }

    public function test_прошедший_день_можно_отметить(): void
    {
        $this->actingAs($this->user())
            ->postJson('/cabinet/pressure', $this->payload([
                'date' => now()->subDays(10)->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertJson(['saved' => true]);
    }

    public function test_будущий_день_отметить_нельзя(): void
    {
        $this->actingAs($this->user())
            ->postJson('/cabinet/pressure', $this->payload([
                'date' => now()->addDay()->format('Y-m-d'),
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('date');

        $this->assertDatabaseCount('blood_pressure_readings', 0);
    }

    public function test_повторная_запись_за_тот_же_день_правит_прежнюю(): void
    {
        $user = $this->user();
        $date = now()->subDay()->format('Y-m-d');

        $this->actingAs($user)->postJson('/cabinet/pressure', $this->payload(['date' => $date]))->assertOk();

        $this->actingAs($user)
            ->postJson('/cabinet/pressure', $this->payload([
                'date' => $date, 'systolic' => 135, 'diastolic' => 88, 'pulse' => 80,
            ]))
            ->assertOk();

        // Вторая отправка за ту же дату — правка, а не вторая строка
        $this->assertDatabaseCount('blood_pressure_readings', 1);
        $this->assertDatabaseHas('blood_pressure_readings', [
            'user_id'  => $user->id,
            'systolic' => 135,
        ]);
    }

    public function test_нечисловые_и_перепутанные_значения_отклоняются(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->postJson('/cabinet/pressure', $this->payload(['systolic' => 'сто двадцать']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('systolic');

        // Верхнее давление не может быть ниже нижнего
        $this->actingAs($user)
            ->postJson('/cabinet/pressure', $this->payload(['systolic' => 70, 'diastolic' => 110]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('systolic');

        $this->assertDatabaseCount('blood_pressure_readings', 0);
    }

    public function test_замер_удаляется(): void
    {
        $user = $this->user();
        $date = now()->format('Y-m-d');

        $this->actingAs($user)->postJson('/cabinet/pressure', $this->payload())->assertOk();

        $this->actingAs($user)
            ->deleteJson("/cabinet/pressure/$date")
            ->assertOk()
            ->assertJson(['deleted' => true]);

        $this->assertDatabaseCount('blood_pressure_readings', 0);
    }

    public function test_не_владелец_не_может_ни_писать_ни_удалять(): void
    {
        $owner = $this->user();
        $stranger = $this->user();
        $date = now()->format('Y-m-d');

        $reading = BloodPressureReading::create([
            'user_id'     => $owner->id,
            'measured_on' => $date,
            'systolic'    => 120,
            'diastolic'   => 80,
            'pulse'       => 70,
        ]);

        // Дневник на сайте один: второй пользователь его только читает
        $this->actingAs($stranger)
            ->postJson('/cabinet/pressure', $this->payload(['systolic' => 190, 'diastolic' => 120, 'pulse' => 99]))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->deleteJson("/cabinet/pressure/$date")
            ->assertForbidden();

        $this->assertDatabaseCount('blood_pressure_readings', 1);
        $this->assertDatabaseHas('blood_pressure_readings', [
            'id'       => $reading->id,
            'user_id'  => $owner->id,
            'systolic' => 120,
        ]);
    }

    public function test_дневник_владельца_виден_гостю_на_главной(): void
    {
        $owner = $this->user();

        BloodPressureReading::create([
            'user_id' => $owner->id, 'measured_on' => now()->format('Y-m-d'),
            'systolic' => 111, 'diastolic' => 70, 'pulse' => 60,
        ]);

        // Гость видит замеры, но формы записи у него нет
        $this->get('/')
            ->assertOk()
            ->assertSee('"sys":111', false)
            ->assertSee('data-can-edit="0"', false)
            ->assertDontSee('id="bpEntryForm"', false);
    }

    public function test_владелец_видит_форму_записи(): void
    {
        $owner = $this->user();

        $this->actingAs($owner)
            ->get('/')
            ->assertOk()
            ->assertSee('data-can-edit="1"', false)
            ->assertSee('id="bpEntryForm"', false);
    }

    public function test_второй_пользователь_видит_дневник_но_без_формы(): void
    {
        $owner = $this->user();
        $stranger = $this->user();

        BloodPressureReading::create([
            'user_id' => $owner->id, 'measured_on' => now()->format('Y-m-d'),
            'systolic' => 111, 'diastolic' => 70, 'pulse' => 60,
        ]);

        $this->actingAs($stranger)
            ->get('/cabinet/pressure')
            ->assertOk()
            ->assertSee('"sys":111', false)
            ->assertSee('data-can-edit="0"', false);
    }
}
