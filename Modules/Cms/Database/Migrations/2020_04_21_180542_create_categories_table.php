<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type', 191)->default('OTHER')->comment('Product Type For Static Processing (OTHER|VIRTUAL_SERVICE|MEDICEN|ETC...)');
            $table->string('slug', 191)->nullable()->unique();
            $table->string('icon', 256)->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('slug');

            $table->foreign('parent_id')
                ->references('id')
                ->on('cms_categories')
                ->onDelete(\DB::raw('SET NULL'));
        });

        Schema::create('cms_categorizables', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->morphs('categorizable');

            $table->foreign('category_id')
                ->references('id')
                ->on('cms_categories')
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
        Schema::dropIfExists('cms_categorizables');
        Schema::dropIfExists('cms_categories');
    }
}
