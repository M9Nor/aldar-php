<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToContentsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table  = 'cms_contents';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->string('currency_code',255)->nullable()->default(null);
            $table->string('currency_symbol',255)->nullable()->default(null);
            $table->string('currency_icon',255)->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('currency_code');
            $table->dropIfExists('currency_symbol');
            $table->dropIfExists('currency_icon');
        });
    }
}