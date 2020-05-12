<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRcaNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rca_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique()->default(null);
            $table->string('value', 255)->default('null');
            $table->enum('type', ['vin', 'body', 'chassis', 'number'])->default('vin');
            $table->dateTime('created_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rca_numbers');
    }
}
