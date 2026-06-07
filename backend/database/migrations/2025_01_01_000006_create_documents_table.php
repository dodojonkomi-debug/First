<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'birth_certificate',    // Шаҳодатномаи таваллуд
                'parent_id',            // Шиноснома/ШҲ-и волидайн
                'medical_form',         // Маълумотномаи тиббӣ (форма 026)
                'vaccination_card',     // Корти эмгузаронӣ
                'residence_certificate' // Маълумотнома аз ҷойи зист
            ]);
            $table->string('original_name'); // Номи аслии файл
            $table->string('file_path');     // Масири файли дар сервер (PDF)
            $table->string('mime_type');
            $table->integer('file_size');    // байт
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
