<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyValueToContentsTable extends Migration
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
            $table->decimal('currency_value',14,2)->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('currency_value');
        });
    }
}