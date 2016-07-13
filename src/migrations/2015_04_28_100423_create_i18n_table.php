<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateI18nTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('i18n', function (Blueprint $table) {
            $table->increments('id');

            $table->string('locale')->unique()->index();
            $table->string('name')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('i18n');
    }
}
