<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectsAndProjectTranslationsTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public $set_schema_table        = 'be_projects';
    public $set_schema_table_trans  = 'be_projects_translations';
    public $set_schema_table_pay    = 'paying_method';
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            if (Schema::hasColumn($this->set_schema_table, 'video')){
                $table->dropColumn('video');
            }
            if (Schema::hasColumn($this->set_schema_table, 'video2')){
                $table->dropColumn('video2');
            }
            if (Schema::hasColumn($this->set_schema_table, 'delivery_date')){
                $table->dropColumn('delivery_date');
            }
        });
        Schema::table($this->set_schema_table_trans, function (Blueprint $table) {
            $table->string('video',255)->nullable()->default(null);
            $table->string('video2',255)->nullable()->default(null);
            $table->string('delivery_date',255)->nullable()->default(null);
        });
        Schema::table($this->set_schema_table_pay, function (Blueprint $table) {
            if (Schema::hasColumn($this->set_schema_table_pay, 'notes')){
                $table->dropColumn('notes');
            }
            $table->text('notes_ar')->nullable()->default(null);
            $table->text('notes_tr')->nullable()->default(null);
            $table->text('notes_en')->nullable()->default(null);
        });
    }
    public function down()
    {
        Schema::table($set_schema_table, function (Blueprint $table) {
            // $table->dropIfExists('keywords');
        });
        Schema::table($set_schema_table_trans, function (Blueprint $table) {
            $table->dropIfExists('video');
            $table->dropIfExists('video2');
            $table->dropIfExists('delivery_date');
        });
        Schema::table($set_schema_table_pay, function (Blueprint $table) {
            $table->dropIfExists('notes_ar');
            $table->dropIfExists('notes_tr');
            $table->dropIfExists('notes_en');
        });
    }
}