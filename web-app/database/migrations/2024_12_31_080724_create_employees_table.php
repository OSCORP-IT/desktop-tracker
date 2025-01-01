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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('permanent_address')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('email');
            $table->string('mobile_number');
            $table->string('password');
            $table->string('security')->nullable();
            $table->string('designation')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
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
        Schema::dropIfExists('employees');
    }
};
