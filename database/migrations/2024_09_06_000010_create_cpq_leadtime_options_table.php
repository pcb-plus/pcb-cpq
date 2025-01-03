<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpqLeadtimeOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpq_leadtime_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leadtime_id');
            $table->string('name', 64);
            $table->string('show_name', 64);
            $table->unsignedInteger('min_days')->default(0);
            $table->unsignedInteger('max_days')->default(0);
            $table->unsignedDouble('price', 10, 4)->default(0);
            $table->unsignedTinyInteger('is_default')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('copy_id')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('leadtime_id', 'leadtime_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpq_leadtime_options');
    }
}
