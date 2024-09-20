<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class HomebrewMore extends Migration
{
    private $tableName = 'homebrew';

    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->text('brew_json')->nullable();
            $table->boolean('deprecated')->nullable();
            $table->string('deprecation_date')->nullable();
            $table->string('deprecation_reason')->nullable();
            $table->bigInteger('install_time')->nullable();
            $table->string('installed_version')->nullable();

            // Make indexes for new columns
            $table->index('deprecated');
            $table->index('deprecation_date');
            $table->index('install_time');
            $table->index('installed_version');

            // Make the existing columns nullable
            $table->text('oldname')->nullable()->change();
            $table->text('aliases')->nullable()->change();
            $table->text('desc')->nullable()->change();
            $table->text('homepage')->nullable()->change();
            $table->text('installed_versions')->nullable()->change();
            $table->text('versions_stable')->nullable()->change();
            $table->text('linked_keg')->nullable()->change();
            $table->text('dependencies')->nullable()->change();
            $table->text('build_dependencies')->nullable()->change();
            $table->text('recommended_dependencies')->nullable()->change();
            $table->text('runtime_dependencies')->nullable()->change();
            $table->text('optional_dependencies')->nullable()->change();
            $table->text('requirements')->nullable()->change();
            $table->text('options')->nullable()->change();
            $table->text('used_options')->nullable()->change();
            $table->text('caveats')->nullable()->change();
            $table->text('conflicts_with')->nullable()->change();
            $table->boolean('built_as_bottle')->nullable()->change();
            $table->boolean('installed_as_dependency')->nullable()->change();
            $table->boolean('installed_on_request')->nullable()->change();
            $table->boolean('poured_from_bottle')->nullable()->change();
            $table->boolean('versions_bottle')->nullable()->change();
            $table->boolean('keg_only')->nullable()->change();
            $table->boolean('outdated')->nullable()->change();
            $table->boolean('pinned')->nullable()->change();
            $table->boolean('versions_devel')->nullable()->change();
            $table->boolean('versions_head')->nullable()->change();
        });
    }

    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('deprecated');
            $table->dropColumn('deprecation_date');
            $table->dropColumn('deprecation_reason');
            $table->dropColumn('install_time');
            $table->dropColumn('installed_version');
        });
    }
}
