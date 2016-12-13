<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsI18nTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contacts_i18n', function (Blueprint $table) {
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->integer('i18n_id')->unsigned();
            $table->foreign('i18n_id')->references('id')->on('i18n')->onDelete('cascade');

            // translatable columns
            $table->string('name', 100);
            $table->string('location', 100);

            $table->unique(['contact_id', 'i18n_id']);

            $table->primary(array('contact_id', 'i18n_id'));

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('contacts_i18n');
    }
}
