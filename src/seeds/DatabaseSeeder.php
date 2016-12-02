<?php

namespace Despark\LaravelDbLocalization;

class DatabaseSeeder extends \Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \DB::table('i18n')->insert([
                'name' => 'English',
                'locale' => 'en',
        ]);

        \DB::table('i18n')->insert([
                'name' => 'Български',
                'locale' => 'bg',
        ]);
    }
}
