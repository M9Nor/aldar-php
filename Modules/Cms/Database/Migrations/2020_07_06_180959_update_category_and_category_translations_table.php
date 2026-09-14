<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategoryAndCategoryTranslationsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table        = 'cms_categories';
    public $set_schema_table_trans  = 'cms_category_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->string('icon_image',255)->nullable();
        });
        Schema::table($this->set_schema_table_trans, function (Blueprint $table) {
            $table->text('seo_description')->nullable();
            $table->text('keywords')->nullable();
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('icon_image');
        });
        Schema::table($set_schema_table_trans, function (Blueprint $table) {
            $table->dropIfExists('seo_description');
            $table->dropIfExists('keywords');
        });
    }
}