<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('counseling_holidays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id'); // ارتباط با پزشک
            $table->unsignedBigInteger('clinic_id')->nullable(); // ارتباط با کلینیک
            $table->json('holiday_dates')->nullable(); // تاریخ‌های تعطیلات مشاوره به‌صورت JSON
            $table->string('status')->default('active'); // وضعیت تعطیلات
            $table->timestamps();

            // کلیدهای خارجی
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_holidays');
    }
};
