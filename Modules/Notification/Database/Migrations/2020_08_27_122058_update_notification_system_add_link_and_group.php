<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationSystemAddLinkAndGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notif_notifications', function (Blueprint $table) {
            $table->string('link', 191)->nullable();
            $table->string('group', 191)->default('BASE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notif_notifications', function (Blueprint $table) {
            $table->dropColumn('link');
            $table->dropColumn('group');
        });
    }
}
