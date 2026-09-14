<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortOrderToCityTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table    = 'cms_cities';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->integer('sort_order')->nullable();
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('sort_order');
        });
    }
}