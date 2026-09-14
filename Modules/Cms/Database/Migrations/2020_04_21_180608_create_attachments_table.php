<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 20)->nullable();
            $table->string('filename');
            $table->string('uid');
            $table->integer('size');
            $table->string('mime', 100);
            $table->string('input_name', 20)->nullable();
            $table->nullableMorphs('attachable');
            $table->unsignedInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by')
            ->references('id')->on('users')
            ->onDelete(\DB::raw('SET NULL'));
        });

        Schema::create('cms_external_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('link')->nullable();
            $table->morphs('attachable');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cms_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('content_id');
            $table->string('type')->nullable();
            $table->string('key')->nullable();
            $table->string('value')->nullable();

            $table->timestamps();

            $table->foreign('content_id')
            ->references('id')->on('cms_contents')
            ->onDelete('CASCADE');
        });

        Schema::create('cms_custom_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 191)->nullable();
            $table->timestamp('disabled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cms_custom_fields_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('custom_field_id');
            $table->string('locale', 5);
            $table->string('title')->nullable();
            $table->string('description')->nullable();

            $table->foreign('custom_field_id')
            ->references('id')->on('cms_custom_fields')
            ->onDelete('CASCADE');
        });

        Schema::create('cms_content_custom_field', function (Blueprint $table) {
            $table->unsignedBigInteger('custom_field_id');
            $table->unsignedBigInteger('content_id');

            $table->foreign('custom_field_id')
            ->references('id')->on('cms_custom_fields')
            ->onDelete('CASCADE');

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
        Schema::dropIfExists('cms_content_custom_field');
        Schema::dropIfExists('cms_custom_fields_translations');
        Schema::dropIfExists('cms_custom_fields');
        Schema::dropIfExists('cms_attributes');
        Schema::dropIfExists('cms_external_attachments');
        Schema::dropIfExists('cms_attachments');
    }
}
