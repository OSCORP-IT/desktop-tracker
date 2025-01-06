<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('name');
            $table->enum('gender', ["Male", "Female", "Oters"]);
            $table->date('date_of_birth');
            $table->string('mobile_number');
            $table->string('email');
            $table->string('password');
            $table->string('security')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('theme_color', ['Light', 'Dark'])->default('Light');
            $table->enum('status', ['Active', 'Inactive', 'Blocked'])->default('Active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
};
