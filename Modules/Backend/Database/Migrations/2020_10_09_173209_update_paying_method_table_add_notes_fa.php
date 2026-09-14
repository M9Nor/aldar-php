<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePayingMethodTableAddNotesFa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paying_method', function (Blueprint $table) {
            $table->text('notes_fa')->nullable()->after('notes_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paying_method', function (Blueprint $table) {
            $table->dropColumn('notes_fa');
        });
    }
}
