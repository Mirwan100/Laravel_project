<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');                   // FK ke projects
            $table->uuid('task_id')->nullable();          // optional ke tasks
            $table->string('filename');
            $table->string('path');                       // lokasi file storage
            $table->dateTime('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->onDelete('cascade');
            $table->foreign('task_id')
                  ->references('id')->on('tasks')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
