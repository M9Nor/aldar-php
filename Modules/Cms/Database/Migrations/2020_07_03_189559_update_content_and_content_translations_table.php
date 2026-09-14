<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContentAndContentTranslationsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table_trans  = 'cms_content_translations';
    public function up()
    {
        Schema::table($this->set_schema_table_trans, function (Blueprint $table) {
            $table->text('seo_description')->nullable()->default(null);
            $table->text('about')->nullable()->default(null);
            $table->text('keywords')->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table_trans, function (Blueprint $table) {
            $table->dropIfExists('seo_description');
            $table->dropIfExists('about');
            $table->dropIfExists('keywords');
        });
    }
}