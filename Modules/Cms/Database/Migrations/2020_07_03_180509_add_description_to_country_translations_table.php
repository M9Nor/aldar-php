<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToCountryTranslationsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table                = 'cms_country_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->text('description')->nullable();
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('description');
        });
    }
}