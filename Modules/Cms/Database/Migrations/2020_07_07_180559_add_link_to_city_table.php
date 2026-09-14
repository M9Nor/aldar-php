<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkToCityTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table                = 'cms_cities';
    public $set_schema_table_translation    = 'cms_city_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->string('link',500)->nullable();
        });
        Schema::table($this->set_schema_table_translation, function (Blueprint $table) {
            $table->string('image',255)->nullable();
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('link');
        });
        Schema::table($set_schema_table_translation, function (Blueprint $table) {
            $table->dropIfExists('image');
        });
    }
}