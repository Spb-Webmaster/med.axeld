<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table): void {
            $table->integer('published')->default(1)->after('coordinates');
            $table->integer('sorting')->default(1)->after('published');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table): void {
            $table->dropColumn(['published', 'sorting']);
        });
    }
};
