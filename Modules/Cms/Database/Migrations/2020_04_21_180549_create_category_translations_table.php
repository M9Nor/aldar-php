<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_category_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('locale', 5);
            $table->string('title', 191)->nullable();
            $table->string('brief', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 256)->nullable();
            $table->string('cover', 256)->nullable();
            $table->unique(
                ['category_id', 'locale']
            );

            $table->foreign('category_id')
            ->references('id')->on('cms_categories')
            ->onDelete('CASCADE');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_category_translations');
    }
}
