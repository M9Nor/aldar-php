<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCmsCategorizablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_categorizables', function (Blueprint $table) {
            $table->string('options')->nullable()->comment('Specify conditions for relation.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms_categorizables', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
