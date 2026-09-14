<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 191)->nullable();
            $table->string('slug', 191)->nullable()->unique();
            $table->string('link')->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('views')->default(0);
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_contents');
    }
}
