<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsCrud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('notif_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('token', 191)->index();
            $table->timestamp('last_activity');
            $table->timestamps();

            $table->unique(
                ['user_id', 'token'],
                'firebase_tokens_unique'
            );
            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');
        });

        Schema::create('notif_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('added_by', ['SYSTEM', 'USER'])->default('SYSTEM');
            $table->string('type', 191)->comment('type to process this notification');
            $table->timestamps();
        });

        Schema::create('notif_notification_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('firebase_notification_id')->unsigned();
            $table->char('locale', 5);
            $table->string('title', 191);
            $table->string('body', 191);
            $table->string('icon', 191)->nullable()->comment('public/firebase/icons');
            $table->string('sound', 191)->nullable()->comment('public/firebase/sounds');
            $table->unique(
                ['firebase_notification_id', 'locale'],
                'fb_notification_translations'
            );
            $table->foreign('firebase_notification_id', 'fb_notification_id_foreign')
            ->references('id')->on('notif_notifications')
            ->onDelete('cascade');
        });

        Schema::create('notif_notification_receivers', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('triggered_by', ['SYSTEM', 'USER', 'ACTION'])->default('SYSTEM');
            $table->integer('notification_id')->unsigned()->nullable();
            $table->integer('from_user_id')->unsigned()->nullable();
            $table->integer('to_user_id')->unsigned()->nullable();
            $table->enum('status', ['PENDING', 'DELIVERED', 'SEEN'])->default('PENDING');
            $table->timestamps();

            $table->foreign('notification_id', 'fb_receiver_notification_id_foreign')
            ->references('id')->on('notif_notifications')
            ->onDelete('cascade');

            $table->foreign('from_user_id', 'fb_from_user_id_foreign')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->foreign('to_user_id', 'fb_to_user_id_foreign')
            ->references('id')->on('users')
            ->onDelete('cascade');

        });

        Schema::create('notif_notification_receiver_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id')->unsigned()->nullable();
            $table->integer('receiver_id')->unsigned()->nullable();
            $table->string('x_key', 191)->comment('KEY to identifiy this data might be to auto replace the body elements with {KEY}');
            $table->text('x_val')->nullable()->comment('type to process this notification');

            $table->foreign('notification_id', 'fb_receiver_data_notification_id_foreign')
            ->references('id')->on('notif_notifications')
            ->onDelete('cascade');

            $table->foreign('receiver_id', 'fb_receiver_data_receiver_id_foreign')
            ->references('id')->on('notif_notification_receivers')
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

        Schema::dropIfExists('notif_notification_receiver_data');
        Schema::dropIfExists('notif_notification_receivers');
        Schema::dropIfExists('notif_notification_translations');
        Schema::dropIfExists('notif_notifications');
        Schema::dropIfExists('notif_tokens');

    }
}
