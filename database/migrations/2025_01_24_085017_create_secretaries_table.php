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
        Schema::create('secretaries', function (Blueprint $table) {
            $table->id(); // شناسه منحصربه‌فرد
            $table->unsignedBigInteger('doctor_id'); // کلید خارجی دکتر
            $table->string('first_name'); // نام
            $table->string('last_name'); // نام خانوادگی
            $table->string('email')->unique()->nullable(); // ایمیل
            $table->string('phone')->unique(); // شماره تماس
            $table->string('national_code')->unique(); // کد ملی
            $table->enum('gender', ['male', 'female']); // جنسیت
            $table->string('address')->nullable(); // آدرس
            $table->date('birth_date')->nullable(); // تاریخ تولد
            $table->string('profile_photo_path')->nullable(); // مسیر عکس پروفایل
            $table->string('password'); // رمز عبور
            $table->boolean('is_active')->default(true); // وضعیت فعال بودن
            $table->timestamp('email_verified_at')->nullable(); // تایید ایمیل
            $table->timestamps(); // زمان‌های ایجاد و به‌روزرسانی
            $table->softDeletes(); // حذف نرم

            // کلید خارجی
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secretaries');
    }
};
