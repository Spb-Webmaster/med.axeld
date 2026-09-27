<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_setting', function (Blueprint $table): void {
            $table->foreignId('setting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->primary(['setting_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_setting');
    }
};
