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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->index();
            $table->foreignId('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreignId('assigned_from_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('assigned_to_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('title');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('due_time')->nullable();
            $table->text('description')->nullable();
            $table->json('sub_tasks')->nullable();
            $table->enum('priority', ["Low", "Medium", "High", "Critical"])->default("Medium");
            $table->string('estimated_time', 8)->nullable(); // e.g.: 20, 2:30
            $table->unsignedBigInteger('order_number');
            $table->enum('status', ["Pending", "In Progress", "Review", "Completed", "Late Completion"])->default('Pending');
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
