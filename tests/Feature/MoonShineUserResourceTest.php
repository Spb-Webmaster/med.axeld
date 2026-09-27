<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Laravel\Models\MoonshineUser;
use Tests\TestCase;

/**
 * Ресурс «Пользователи сайта» в админке: страницы открываются, пункт меню виден,
 * учётная запись заводится и её паролем можно войти в личный кабинет.
 */
class MoonShineUserResourceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): MoonshineUser
    {
        return MoonshineUser::create([
            'moonshine_user_role_id' => 1,
            'name'                   => 'Админ',
            'email'                  => 'admin@example.test',
            'password'               => 'Admin12345!',
        ]);
    }

    public function test_пункт_меню_виден_в_панели(): void
    {
        $this->actingAs($this->admin(), 'moonshine')
            ->get('/admin')
            ->assertOk()
            ->assertSee('Пользователи сайта')
            ->assertSee('resource/user-resource');
    }

    public function test_страницы_ресурса_открываются(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $this->actingAs($admin, 'moonshine')
            ->get('/admin/resource/user-resource/user-index-page')
            ->assertOk()
            ->assertSee($user->email);

        $this->actingAs($admin, 'moonshine')
            ->get('/admin/resource/user-resource/user-form-page')
            ->assertOk();

        $this->actingAs($admin, 'moonshine')
            ->get('/admin/resource/user-resource/user-form-page?resourceItem=' . $user->id)
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_пользователь_заводится_из_админки_и_может_войти(): void
    {
        $this->actingAs($this->admin(), 'moonshine')
            ->post('/admin/resource/user-resource/crud', [
                'name'                  => 'Заведён из панели',
                'email'                 => 'created@example.test',
                'phone'                 => '79997776655',
                'birth_date'            => '1990-01-15',
                'published'             => 1,
                'password'              => 'FromAdmin123!',
                'password_confirmation' => 'FromAdmin123!',
            ])
            // Обычная отправка формы в MoonShine заканчивается редиректом,
            // поэтому важен не 2xx, а отсутствие ошибок валидации
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user = User::where('email', 'created@example.test')->first();

        $this->assertNotNull($user, 'Пользователь не создан');
        $this->assertSame('79997776655', $user->phone);
        $this->assertTrue($user->published);

        // Пароль не должен оказаться захэширован дважды — иначе вход не сработает
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('FromAdmin123!', $user->password),
            'Пароль из админки не совпадает с хэшем — похоже на двойное хэширование'
        );

        /*
         | actingAs($admin, 'moonshine') сделал guard админки основным на весь тест.
         | Запрос к сайту приходит в guard web — возвращаем его, иначе auth()->attempt()
         | в LoginController искал бы клиента в таблице администраторов.
         */
        auth()->shouldUse('web');

        $this->post('/login', [
            'email'    => 'created@example.test',
            'password' => 'FromAdmin123!',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cabinet'));

        $this->assertAuthenticatedAs($user, 'web');
    }
}
