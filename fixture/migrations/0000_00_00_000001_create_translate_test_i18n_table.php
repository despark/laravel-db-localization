<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('translate_test_i18n', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id');
            $table->string('locale');
            $table->string('field_1');
            $table->string('field_2');
            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('translate_test')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translate_test');
    }
}
