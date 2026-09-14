<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotifNotificationTranslationsAddImageField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notif_notification_translations', function (Blueprint $table) {
            $table->string('image', 256)->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notif_notification_translations', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
