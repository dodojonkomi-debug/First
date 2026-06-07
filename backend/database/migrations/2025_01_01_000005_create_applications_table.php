<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_code')->unique(); // Формат: {сол}-{моҳ}-{рӯз}-{соат}-MTMU__-{XXXX}
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Волидайн
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            // Маълумоти кӯдак
            $table->string('child_first_name');
            $table->string('child_last_name');
            $table->string('child_middle_name')->nullable();
            $table->date('child_birth_date');
            $table->enum('child_gender', ['male', 'female']);

            // Синф: 0 (6-сола) ё 1 (7-сола)
            $table->enum('grade', ['0', '1']);

            // Маълумоти волидайн
            $table->string('parent_first_name');
            $table->string('parent_last_name');
            $table->string('parent_id_number'); // Рақами шиноснома
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();

            // Ҷойи зист
            $table->foreignId('residence_region_id')->constrained('regions');
            $table->foreignId('residence_district_id')->constrained('districts');
            $table->string('residence_address'); // Маҳалла ва суроға

            // Статус
            $table->enum('status', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            // Чеклист тасдиқи ҳуҷҷатҳо
            $table->boolean('has_birth_certificate')->default(false);
            $table->boolean('has_parent_id')->default(false);
            $table->boolean('has_medical_form')->default(false);
            $table->boolean('has_vaccination_card')->default(false);
            $table->boolean('has_residence_certificate')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
