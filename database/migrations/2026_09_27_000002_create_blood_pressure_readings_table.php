<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Замеры давления и пульса — по одному на день на пользователя.
 *
 * Уникальный ключ (user_id, measured_on) держит это правило на уровне базы:
 * календарь рисует одну ячейку на дату, и повторная запись за тот же день —
 * это правка прежней, а не вторая строка.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_pressure_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('measured_on');

            // Значения заведомо влезают в small int: верхняя граница проверки — 300
            $table->unsignedSmallInteger('systolic');
            $table->unsignedSmallInteger('diastolic');
            $table->unsignedSmallInteger('pulse');

            $table->timestamps();

            $table->unique(['user_id', 'measured_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_pressure_readings');
    }
};
