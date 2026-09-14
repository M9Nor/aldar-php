<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectImagesToProjectTranslationsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table  = 'be_projects_translations';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->string('project_image1',255)->nullable()->default(null);
            $table->string('project_image2',255)->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            $table->dropIfExists('project_image1');
            $table->dropIfExists('project_image2');
        });
    }
}