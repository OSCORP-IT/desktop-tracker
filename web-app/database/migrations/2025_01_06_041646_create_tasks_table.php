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
            $table->unsignedBigInteger('project_id')->index()->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->text('title');
            $table->unsignedBigInteger('assigned_to')->index();
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->text('description')->nullable();
            $table->json('sub_tasks')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('priority', ["High", "Medium", "Low"])->default("Medium");
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
