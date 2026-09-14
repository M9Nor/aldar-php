<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Backend\Entities\Project;

class UpdateAttributesTableAddPolymorphic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_attributes', function (Blueprint $table) {
            $table->dropForeign(['content_id']);
            $table->string('content_type')->default('Modules\\Cms\\Entities\\Content')->after('content_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms_attributes', function (Blueprint $table) {
            $table->foreign('content_id')
            ->references('id')->on('cms_contents')
            ->onDelete('CASCADE');
            $table->dropIfExists('content_type');
        });
    }
}
