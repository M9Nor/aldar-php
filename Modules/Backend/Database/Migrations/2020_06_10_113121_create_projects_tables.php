<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTables extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('be_projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 191)->nullable()->unique();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('content_id')->nullable();

            $table->string('status', 20)->default('ACTIVE')->comment('ACTIVE|INACTIVE');
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->string('link')->nullable();
            $table->string('location')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('establishment_date')->nullable();
            $table->Integer('views')->nullable();
            $table->Integer('likes')->nullable();

            $table->string('sea')->nullable();
            $table->string('city_distance')->nullable();
            $table->string('airport')->nullable();
            $table->string('school')->nullable();
            $table->string('university')->nullable();
            $table->string('hospital')->nullable();
            $table->string('mosque')->nullable();
            $table->string('mall')->nullable();
            $table->string('details_area')->nullable();

            $table->unsignedBigInteger('area_id')->unsigned();
            
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();

            $table->index('slug');

            $table->foreign('city_id')
                ->references('id')
                ->on('cms_cities')
                ->onDelete(\DB::raw('SET NULL'));
           
                $table->foreign('area_id')
                ->references('id')->on('cms_areas')
                ->onDelete('cascade');

            $table->foreign('country_id')
                ->references('id')
                ->on('cms_countries')
                ->onDelete(\DB::raw('SET NULL'));


            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('content_id')
            ->references('id')->on('cms_contents')
            ->onDelete('CASCADE');


        });
        Schema::create('be_projects_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('project_id')->unsigned()->index();
            $table->string('locale', 5)->default('ar');
            $table->string('title', 500);
            $table->longtext('description')->nullable();
            $table->longtext('details')->nullable();
            $table->longtext('about')->nullable();
            $table->string('image')->nullable();
            $table->text('brief')->nullable();

            $table->foreign('project_id')
            ->references('id')->on('be_projects')
            ->onDelete('cascade');
        });

        Schema::create('be_projects_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('project_id')->unsigned()->index();
            $table->unsignedBigInteger('area_id')->unsigned()->index();
           
            $table->foreign('area_id')
            ->references('id')->on('cms_areas')
            ->onDelete('cascade');

            $table->foreign('project_id')
            ->references('id')->on('be_projects')
            ->onDelete('cascade');
        });

        Schema::create('be_projects_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('project_id')->unsigned()->index();
            $table->unsignedBigInteger('content_id')->unsigned()->index();
           
            $table->foreign('content_id')
            ->references('id')->on('cms_contents')
            ->onDelete('cascade');

            $table->foreign('project_id')
            ->references('id')->on('be_projects')
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
        Schema::dropIfExists('be_projects_contents');
        Schema::dropIfExists('be_projects_areas');
        Schema::dropIfExists('be_projects_translations');
        Schema::dropIfExists('be_projects');
    }
}
