<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('insurance_id')->nullable();

            // فیلدهای مشابه جدول نوبت‌ها
            $table->integer('duration')->nullable(); // مدت زمان مشاوره (دقیقه)
            $table->enum('consultation_type', ['general', 'specialized', 'emergency'])->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('payment_status', ['pending', 'paid', 'unpaid'])->default('pending');

            $table->enum('consultation_mode', ['in_person', 'online', 'phone']);
            $table->date('consultation_date');
            $table->time('start_time');
            $table->time('end_time');

            // فیلدهای اضافی برای مدیریت دقیق مشاوره‌ها
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('attendance_status', ['attended', 'missed', 'cancelled'])->default('attended');
            $table->text('notes')->nullable();
            $table->text('topic')->nullable(); // موضوع مشاوره
            $table->string('tracking_code')->nullable()->unique();
            $table->decimal('fee', 8, 2)->nullable();
            $table->boolean('notification_sent')->default(false);

            // ارتباطات و کلیدهای خارجی
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('insurance_id')->references('id')->on('insurances')->onDelete('set null');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
