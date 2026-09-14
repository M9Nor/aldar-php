<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',191)->nullable();
            $table->integer('views')->nullable();
            $table->unsignedInteger('added_by')->nullable();

            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('added_by')
            ->references('id')->on('users')
            ->onDelete(\DB::raw('SET NULL'));
        });
        Schema::create('cms_tag_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->unsigned()->index();
            $table->string('locale', 5)->default('ar');
            $table->string('text', 20);

            $table->foreign('tag_id')
            ->references('id')->on('cms_tags')
            ->onDelete('cascade');
        });
        Schema::create('cms_taggables',function(Blueprint $table) {
            $table->morphs('taggable');
            $table->integer('tag_id')->unsigned()->index();
            $table->foreign('tag_id')
            ->references('id')->on('cms_tags')
            ->onDelete('cascade');

        });
        Schema::create('cms_keywords', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',191)->nullable();
            $table->integer('views')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('cms_keyword_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('key_id')->unsigned()->index();
            $table->string('locale', 4)->default('ar');
            $table->string('text', 20);

            $table->foreign('key_id')
            ->references('id')->on('cms_keywords')
            ->onDelete('cascade');
        });
        Schema::create('cms_keywordables',function(Blueprint $table) {
            $table->increments('id');
            $table->morphs('keywordable');
            $table->integer('keyword_id')->unsigned()->index();
            $table->foreign('keyword_id')
            ->references('id')->on('cms_keywords')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_keywordables');
        Schema::dropIfExists('cms_keyword_translations');
        Schema::dropIfExists('cms_keywords');
        Schema::dropIfExists('cms_taggables');
        Schema::dropIfExists('cms_tag_translations');
        Schema::dropIfExists('cms_tags');

    }
}
