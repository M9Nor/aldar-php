<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone', 20)->unique()->nullable();
            $table->string('username', 191)->unique()->nullable();
            $table->string('first_name', 191)->nullable();
            $table->string('last_name', 191)->nullable();
            $table->string('email', 191)->unique()->nullable();
            $table->string('status', 20)->default('PENDING')->comment('PENDING|ACTIVE|SUSPENDED');
            $table->boolean('online')->nullable()->default(true);
            $table->string('sex', 20)->nullable()->default('UNSPECIFIED')->comment('UNSPECIFIED|MALE|FEMALE');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->string('image', 256)->nullable();
            $table->string('address', 256)->nullable();
            $table->string('verification_code', 20)->nullable()->comment('NULL|VERIFIED|XXXX');
            $table->string('locale', 5)->default('ar');
            $table->rememberToken();
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('users');
     }
}
