<?php

use Silber\Bouncer\Database\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Models::table('groups'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('icon');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(Models::table('group_translations'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('group_id')->nullable();
            $table->string('locale', 5)->default(\Lang::getLocale());
            $table->string('title')->nullable();
            $table->string('description')->nullable();

            $table->index(["group_id"], 'perms_group_translations_group_id_index');

            $table->unique(["group_id", "locale"], 'perms_group_translations_unique');

            $table->foreign('group_id', 'perms_group_translations_unique')
                ->references('id')->on('perms_groups')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });

        Schema::create(Models::table('abilities'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('group_id')->nullable();
            $table->string('name');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('only_owned')->default(false);
            $table->text('options')->nullable();
            $table->integer('scope')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(["group_id"], 'perms_abilities_group_id_index');

            $table->foreign('group_id', 'perms_abilities_unique')
                ->references('id')->on(Models::table('groups'))
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });

        Schema::create(Models::table('ability_translations'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('ability_id')->nullable();
            $table->string('locale', 5)->default(\Lang::getLocale());
            $table->string('title', 100)->nullable();
            $table->string('description', 500)->nullable();

            $table->index(["ability_id"], 'perms_ability_translations_ability_id_index');

            $table->unique(["ability_id", "locale"], 'perms_ability_translations_unique');

            $table->foreign('ability_id', 'perms_ability_translations_unique')
                ->references('id')->on(Models::table('abilities'))
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });

        Schema::create(Models::table('roles'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('color', 10)->nullable();
            $table->integer('level')->unsigned()->nullable();
            $table->integer('scope')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['name', 'scope'],
                'perms_roles_name_unique'
            );
        });

        Schema::create(Models::table('role_translations'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('locale', 5)->default('ar');
            $table->string('title', 100)->nullable();
            $table->string('description', 500)->nullable();

            $table->index(["role_id"], 'perms_role_translations_role_id_index');

            $table->unique(["role_id", "locale"], 'perms_role_translations_unique');

            $table->foreign('role_id', 'perms_role_translations_unique')
                ->references('id')->on(Models::table('roles'))
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });

        Schema::create(Models::table('assigned_roles'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id')->unsigned()->index();
            $table->bigInteger('entity_id')->unsigned();
            $table->string('entity_type');
            $table->bigInteger('restricted_to_id')->unsigned()->nullable();
            $table->string('restricted_to_type')->nullable();
            $table->integer('scope')->nullable()->index();

            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'perms_assigned_roles_entity_index'
            );

            $table->foreign('role_id')
                  ->references('id')->on(Models::table('roles'))
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create(Models::table('permissions'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ability_id')->unsigned()->index();
            $table->bigInteger('entity_id')->unsigned()->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('forbidden')->default(false);
            $table->integer('scope')->nullable()->index();

            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'perms_permissions_entity_index'
            );

            $table->foreign('ability_id')
                  ->references('id')->on(Models::table('abilities'))
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create(Models::table('manageables'), function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned()->index();
            $table->bigInteger('manageable_id')->unsigned()->nullable();
            $table->string('manageable_type')->nullable();

            $table->index(
                ['manageable_id', 'manageable_type']
            );

            $table->foreign('role_id')
                  ->references('id')->on(Models::table('roles'))
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Models::table('manageables'));
        Schema::drop(Models::table('permissions'));
        Schema::drop(Models::table('assigned_roles'));
        Schema::drop(Models::table('role_translations'));
        Schema::drop(Models::table('roles'));
        Schema::drop(Models::table('ability_translations'));
        Schema::drop(Models::table('abilities'));
        Schema::drop(Models::table('group_translations'));
        Schema::drop(Models::table('groups'));
    }
}
