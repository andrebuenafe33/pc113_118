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
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();

            // Who assigned the task (admin or manager)
            $table->unsignedBigInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');

            // Who the task is assigned to (staff)
            $table->unsignedBigInteger('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('task_assignments');
    }
};
