<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['news'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('subtitle')->nullable();
                $table->text('short_desc')->nullable();
                $table->string('img')->nullable();
                $table->text('desc')->nullable();
                $table->string('img2')->nullable();
                $table->text('desc2')->nullable();
                $table->text('html')->nullable();
                $table->text('html2')->nullable();
                $table->integer('published')->default(1);
                $table->text('params')->nullable();
                $table->string('video')->nullable();
                $table->text('gallery')->nullable();
                $table->text('files')->nullable();
                $table->string('metatitle')->nullable();
                $table->string('description')->nullable();
                $table->string('keywords')->nullable();
                $table->text('script')->nullable();
                $table->integer('sorting')->default(1);
                $table->text('faq')->nullable();
                $table->text('custom_field')->nullable();
                $table->text('custom_field2')->nullable();
                $table->text('custom_field3')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach (['news'] as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
