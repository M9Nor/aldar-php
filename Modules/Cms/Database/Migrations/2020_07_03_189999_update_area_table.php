<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAreaTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table_trans  = 'cms_area_translations';
    public function up()
    {
        Schema::table($this->set_schema_table_trans, function (Blueprint $table) {
            $table->string('image',255)->nullable()->default(null);
            $table->text('about')->nullable()->default(null);
            $table->text('short_description')->nullable()->default(null);
            $table->text('details')->nullable()->default(null);
            $table->text('keywords')->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table_trans, function (Blueprint $table) {
            $table->dropIfExists('image');
            $table->dropIfExists('about');
            $table->dropIfExists('short_description');
            $table->dropIfExists('details');
            $table->dropIfExists('keywords');
        });
    }
}