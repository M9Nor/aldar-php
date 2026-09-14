<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dial_code', 10)->nullable();
            $table->string('latitude', 40)->nullable();
            $table->string('longitude', 40)->nullable();

            $table->nullableTimestamps();
            $table->softDeletes();
        });

        Schema::create('cms_country_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('locale', 5);
            $table->string('name', 191)->nullable();
                $table->unique(
                    ['country_id', 'locale']
                );
                $table->foreign('country_id')
                ->references('id')->on('cms_countries')
                ->onDelete('cascade');
            });

        Schema::create('cms_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('native_name', 191)->nullable();
            $table->string('zip_code', 191)->nullable();
            $table->string('lat', 100)->nullable();
            $table->string('lng', 100)->nullable();

            $table->nullableTimestamps();
            $table->softDeletes();

            $table->foreign('country_id')
            ->references('id')->on('cms_countries')
            ->onDelete('cascade');
        });

        Schema::create('cms_city_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->unsigned();
            $table->char('locale', 5);
            $table->string('name', 191)->nullable();
            $table->unique(
                ['city_id', 'locale']
            );
            $table->foreign('city_id')
            ->references('id')->on('cms_cities')
            ->onDelete('cascade');
        });
        /////////area

        Schema::create('cms_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('city_id')->unsigned();
            $table->string('native_name', 191)->nullable();
            $table->string('zip_code', 191)->nullable();
            $table->string('lat', 100)->nullable();
            $table->string('lng', 100)->nullable();
            $table->longtext('description')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->foreign('city_id')
            ->references('id')->on('cms_cities')
            ->onDelete('cascade');
        });

        Schema::create('cms_area_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('area_id')->unsigned()->index();
            $table->char('locale', 5);
            $table->string('name', 191)->nullable();
            $table->longtext('description')->nullable();
            $table->unique(
                ['area_id', 'locale']
            );
            $table->foreign('area_id')
            ->references('id')->on('cms_areas')
            ->onDelete('cascade');
        });
        /////////////////////////////////////////////
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->nullable()->after('image');
            $table->unsignedInteger('city_id')->nullable()->after('country_id');

            $table->foreign('city_id')
                ->references('id')
                ->on('cms_cities')
                ->onDelete(\DB::raw('SET NULL'));

            $table->foreign('country_id')
                ->references('id')
                ->on('cms_countries')
                ->onDelete(\DB::raw('SET NULL'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
        });
        Schema::dropIfExists('cms_area_translations');
        Schema::dropIfExists('cms_areas');
        Schema::dropIfExists('cms_city_translations');
        Schema::dropIfExists('cms_cities');
        Schema::dropIfExists('cms_country_translations');
        Schema::dropIfExists('cms_countries');
    }
}
