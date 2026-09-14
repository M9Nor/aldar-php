<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeywordsToProjectsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table        = 'be_projects';
    public $set_schema_table_trans  = 'be_projects_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->text('keywords')->nullable()->default(null);
        });
        Schema::table($this->set_schema_table_trans, function (Blueprint $table) {
            $table->text('seo_description')->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('keywords');
        });
        Schema::table($set_schema_table_trans, function (Blueprint $table) {
            $table->dropIfExists('seo_description');
        });
    }
}