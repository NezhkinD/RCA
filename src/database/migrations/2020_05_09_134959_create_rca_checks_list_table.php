<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcaChecksListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rca_checks_list', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->dateTime('created_at', 6);

            $table->dateTime('updated_at', 6);

            $table->enum('status', ['wait', 'work', 'error', 'done'])
                ->default('wait');

            $table->jsonb('results')
                ->default(null);

            $table->bigInteger('number')
                ->unsigned()
                ->default(0);

            $table->boolean('received')
                ->default(false);

            $table->foreign('number')
                ->references('id')->on('rca_numbers')
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
        Schema::dropIfExists('rca_checks_list');
    }
}
