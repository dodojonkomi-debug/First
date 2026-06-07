<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Номи мактаб
            $table->string('code')->unique(); // Коди мактаб
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->integer('capacity_class_0')->default(0); // Ҷойҳои холӣ барои синфи 0
            $table->integer('capacity_class_1')->default(0); // Ҷойҳои холӣ барои синфи 1
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
