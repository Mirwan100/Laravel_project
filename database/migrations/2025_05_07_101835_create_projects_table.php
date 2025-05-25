<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();                // UUID PK
            $table->string('name');                       // nama project
            $table->json('settings')->nullable();         // JSON untuk opsi-opsi
            $table->dateTime('start_at')->nullable();     // tanggal mulai
            $table->boolean('is_active')->default(true);  // status aktif/inaktif
            $table->timestamps();
            $table->softDeletes();                        // soft delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
