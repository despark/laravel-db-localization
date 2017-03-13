<?php


namespace Despark\Tests\LaravelDbLocalization;


use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Illuminate\Database\Schema\Blueprint;

abstract class AbstractTestCase extends AbstractPackageTestCase
{

    /**
     * Setup the application environment.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->config->set('app.key', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');

        $app->config->set('cache.driver', 'array');

        $app->config->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => env('TEST_DB_DATABASE', 'translation_tests'),
            'username' => env('TEST_DB_USERNAME', 'test'),
            'password' => env('TEST_DB_PASSWORD', 'test'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        $app->config->set('mail.driver', 'log');

        $app->config->set('session.driver', 'array');


        $app->setLocale('en');

        $this->createTestTables();
    }

    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return [];
    }

    public function createTestTables()
    {
        if (! \Schema::hasTable('translate_test')) // We need to create the table
        {
            \Schema::create('translate_test', function (Blueprint $table) {
                $table->increments('id');
                $table->string('not_translatable');
                $table->timestamps();
            });
        }

        if (! \Schema::hasTable('translate_test_i18n')) // Creeate i18n table
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
    }

}