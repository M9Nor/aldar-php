<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTagsAndTagTranslationTables extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table        = 'cms_tags';
    public $set_schema_table_trans  = 'cms_tag_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->text('keywords')->nullable()->default(null);
        });
        Schema::table($this->set_schema_table_trans, function (Blueprint $table) {
            $table->string('image',255)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('keywords');
        });
        Schema::table($set_schema_table_trans, function (Blueprint $table) {
            $table->dropIfExists('image');
            $table->dropIfExists('description');
        });
    }
}