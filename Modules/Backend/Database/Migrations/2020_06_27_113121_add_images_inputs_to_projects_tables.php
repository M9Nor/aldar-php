<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImagesInputsToProjectsTables extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table                = 'be_projects';
    public $set_schema_table_translation    = 'be_projects_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->renameColumn('link','video2');
            $table->string('pro_image',191)->nullable()->default(null);
            $table->string('pro_image2',191)->nullable()->default(null);
        });
        Schema::table($this->set_schema_table_translation, function (Blueprint $table) {
            if (Schema::hasColumn($this->set_schema_table_translation, 'breef')){
                $table->renameColumn('breef','brief');
            }
        }); 
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            // $table->dropIfExists('link');
            $table->dropIfExists('pro_image');
            $table->dropIfExists('pro_image2');
        });
        Schema::table($set_schema_table_translation, function (Blueprint $table) {
            $table->dropIfExists('brief');
        });
    }
}