<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpqLeadtimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpq_leadtimes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('title');
            $table->unsignedTinyInteger('is_conditional')->default(0);
            $table->text('condition_expression')->nullable();
            $table->text('condition_description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('copy_id')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('product_id', 'product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpq_leadtimes');
    }
}
