<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAuditsMakeIdsString extends Migration
{
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            // change auditable_id from bigint → string(36)
            $table->string('auditable_id', 36)->change();

            // if your users are also UUID, change user_id too
            $table->string('user_id', 36)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            // revert back to unsignedBigInteger if you like
            $table->unsignedBigInteger('auditable_id')->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
}





