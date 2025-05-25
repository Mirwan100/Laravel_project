<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');                   // FK ke projects.id
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();         // misal: tag, tambahan data
            $table->dateTime('due_at')->nullable();       // tenggat
            $table->boolean('done')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
