<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTurkishNationalityToProjectsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table    = 'be_projects';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->tinyInteger('turkish_nationality')->default(0);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('turkish_nationality');
        });
    }
}