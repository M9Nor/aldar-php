<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paying_method', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->integer('project_type_id')->nullable();

            $table->string('type',191)->nullable();
            $table->string('first_pay',191)->nullable();
            $table->string('number_id',191)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->foreign('project_id')
            ->references('id')->on('be_projects')
            ->onDelete('cascade');
        });
        Schema::create('price', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->unsigned()->index();
            $table->unsignedBigInteger('balance_id')->unsigned()->index();
           
            $table->string('balance',191)->nullable();
            // $table->unsignedBigInteger('project_id')->nullable();
            $table->integer('highest_price')->nullable();
            $table->integer('lowest_price')->nullable();
            $table->integer('highest_area')->nullable();
            $table->integer('lowest_area')->nullable();
            $table->integer('bathes_number')->nullable();
            $table->integer('salons_number')->nullable();
            $table->integer('room_number')->nullable();
            $table->timestamps();
            $table->foreign('project_id')
            ->references('id')->on('be_projects')
            ->onDelete('cascade');
            
            $table->foreign('balance_id')
            ->references('id')->on('cms_contents')
            ->onDelete('cascade');
        });
        Schema::create('finance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('type',191)->nullable();
            $table->string('first_pay',191)->nullable();
            $table->string('number',191)->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
        Schema::dropIfExists('finance');
        Schema::dropIfExists('price');
        Schema::dropIfExists('paying_method');
    }
}
