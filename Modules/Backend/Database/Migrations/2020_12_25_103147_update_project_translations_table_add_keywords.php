<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Backend\Entities\Project;

class UpdateProjectTranslationsTableAddKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('be_projects_translations', function (Blueprint $table) {
            $table->text('trans_keywords')->nullable()->after('seo_description');
        });

        try {
            DB::transaction(function () {
                $projectsWithKeywords = Project::whereNotNull('keywords')->get();

                foreach ($projectsWithKeywords as $key => $project) {
                    $project->{'trans_keywords:ar'}  = $project->keywords;
                    $project->save();
                }
            });
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('be_projects_translations', function (Blueprint $table) {
            $table->dropIfExists('trans_keywords');
        });
    }
}
