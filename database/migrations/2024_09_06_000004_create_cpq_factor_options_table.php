<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpqFactorOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpq_factor_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('factor_id');
            $table->string('name', 64);
            $table->string('show_name', 64);
            $table->string('value', 64);
            $table->string('description', 255);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('copy_id')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['factor_id', 'value'], 'factor_id_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpq_factor_options');
    }
}
