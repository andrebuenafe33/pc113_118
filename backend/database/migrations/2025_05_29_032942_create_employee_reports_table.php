<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('employee_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('task_assignment_id');
            $table->foreign('task_assignment_id')->references('id')->on('task_assignments')->onDelete('cascade');

            $table->unsignedBigInteger('submitted_by'); // staff user id
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');

            $table->text('report');
            $table->string('attachment')->nullable(); // file path if any

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('employee_reports');
    }
};
