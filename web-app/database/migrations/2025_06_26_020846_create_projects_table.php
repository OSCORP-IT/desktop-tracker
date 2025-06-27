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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('overview')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->double('budget')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->enum('status', ["Pending", "Ongoing", "Finished", "Archived"])->default('Pending');
            $table->unsignedBigInteger('created_by_id')->index();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('end_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
