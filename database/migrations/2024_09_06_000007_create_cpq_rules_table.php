<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpqRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpq_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cost_id');
            $table->unsignedDouble('price', 10, 4)->default(0);
            $table->unsignedTinyInteger('is_unit')->default(0);
            $table->text('multiplier_expression')->nullable();
            $table->text('multiplier_description')->nullable();
            $table->unsignedTinyInteger('is_conditional')->default(0);
            $table->text('condition_expression')->nullable();
            $table->text('condition_description')->nullable();
            $table->unsignedTinyInteger('is_tiered')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('copy_id')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('cost_id', 'cost_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpq_rules');
    }
}
