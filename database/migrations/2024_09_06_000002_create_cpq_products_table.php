<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpqProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpq_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('version_id');
            $table->string('name', 64);
            $table->string('show_name', 64);
            $table->string('code', 64);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('copy_id')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['version_id', 'code'], 'version_id_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpq_products');
    }
}
