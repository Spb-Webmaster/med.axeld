<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Поля пользователя сайта для личного кабинета.
 *
 * Учётные записи заводит администратор в MoonShine (App\MoonShine\Resources\User),
 * поэтому нужен выключатель published: снятая отметка закрывает вход,
 * но сохраняет саму запись и её историю.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->boolean('published')->default(true)->after('birth_date')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['published']);
            $table->dropColumn(['phone', 'birth_date', 'published']);
        });
    }
};
