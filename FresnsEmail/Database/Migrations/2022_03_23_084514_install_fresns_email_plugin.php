<?php

use Plugins\FresnsEmail\Support\Installer;
use Illuminate\Database\Migrations\Migration;

class InstallFresnsEmailPlugin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new Installer)->install();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        (new Installer)->uninstall(true);
    }
}
