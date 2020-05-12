<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcaChecksLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rca_checks_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('created_at', 6);
            $table->dateTime('updated_at', 6);

            $table->bigInteger('check')->unsigned()->default(null);

            $table->foreign('check')
                ->references('id')->on('rca_checks_list')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rca_checks_logs');
    }
}
