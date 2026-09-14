<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_content_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('content_id');
            $table->string('locale', 5);
            $table->string('link')->nullable();
            $table->string('title', 191)->nullable();
            $table->string('brief', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('image', 256)->nullable();
            $table->string('cover', 256)->nullable();

            $table->unique(
                ['content_id', 'locale']
            );

            $table->foreign('content_id')
            ->references('id')->on('cms_contents')
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
        Schema::dropIfExists('cms_content_translations');
    }
}
