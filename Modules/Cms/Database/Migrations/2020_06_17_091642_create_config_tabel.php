<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sort_order')->nullable();
            $table->string('validations')->default('required');
            $table->string('input_type')->default('TEXT');
            $table->string('key');
            $table->string('val');
            $table->longtext('url_link')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->boolean('has_additional_info')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('cms_config_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('config_id');
            $table->string('locale', 5);
            $table->string('label', 191);
            $table->string('placeholder', 191)->nullable();
            $table->string('help', 500)->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->unique(
                ['config_id', 'locale']
            );

            $table->foreign('config_id')
                ->references('id')->on('cms_configs')
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
        Schema::dropIfExists('cms_config_translations');
        Schema::dropIfExists('cms_configs');
    }
}
