<?php


namespace Despark\Tests\LaravelDbLocalization;


use Despark\Tests\LaravelDbLocalization\TestClasses\TranslateModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class TranslationTest.
 */
class TranslationTest extends AbstractTestCase
{

    use DatabaseTransactions;


    /**
     *
     */
    public function testCreatingTranslation()
    {
        $translateModel = new TranslateModel();

        $translateModel->not_translatable = 'Test';

        $translateModel->field_1 = 'test';
        $translateModel->field_2 = 'test2';

        $translateModel->setTranslation('field_1', 'bg', 'тест');
        $translateModel->setTranslation('field_2', 'bg', 'тест2');

        $translateModel->save();

        $exists = \DB::table('translate_test')
                     ->where('id', $translateModel->getKey())
                     ->where('not_translatable', 'Test')
                     ->exists();

        $this->assertTrue($exists);

        // Refresh translations to get raw data.
        $translateModel->refreshTranslations();

        $this->assertEquals('тест', $translateModel->getTranslation('field_1', 'bg'));
        $this->assertEquals('тест2', $translateModel->getTranslation('field_2', 'bg'));
        $this->assertEquals('test', $translateModel->getTranslation('field_1', 'en'));
        $this->assertEquals('test2', $translateModel->getTranslation('field_2', 'en'));
        // Test non-existent
        $this->assertNull($translateModel->getTranslation('field_4', 'bg'));

        // Check default locale
        $this->assertEquals('test', $translateModel->field_1);
        $this->assertEquals('test2', $translateModel->field_2);

        // Check if we switch locales
        $this->app->setLocale('bg');
        $this->assertEquals('тест', $translateModel->field_1);
        $this->assertEquals('тест2', $translateModel->field_2);

    }

    /**
     *
     */
    public function testLoadTranslations()
    {
        $translateModel = new TranslateModel();

        $translateModel->not_translatable = 'Test';

        //        $translateModel->setTranslation('field_1', 'bg', 'тест');
        //        $translateModel->setTranslation('field_2', 'bg', 'тест2');

        $translateModel->save();

        \DB::table('translate_test_i18n')->insert(
            [
                [
                    'parent_id' => $translateModel->getKey(),
                    'locale' => 'bg',
                    'field_1' => 'тест',
                    'field_2' => 'тест2',
                ],
                [
                    'parent_id' => $translateModel->getKey(),
                    'locale' => 'en',
                    'field_1' => 'test',
                    'field_2' => 'test2',
                ],
            ]
        );


        $translateModel->loadTranslations();

        $this->assertNotEmpty($translateModel->getTranslatedAttributes());
    }


    /**
     *
     */
    public function testUpdateTranslation()
    {
        $translateModel = new TranslateModel();

        $translateModel->not_translatable = 'Test';

        //        $translateModel->setTranslation('field_1', 'bg', 'тест');
        //        $translateModel->setTranslation('field_2', 'bg', 'тест2');

        $translateModel->save();

        \DB::table('translate_test_i18n')->insert(
            [
                [
                    'parent_id' => $translateModel->getKey(),
                    'locale' => 'bg',
                    'field_1' => 'тест',
                    'field_2' => 'тест2',
                ],
                [
                    'parent_id' => $translateModel->getKey(),
                    'locale' => 'en',
                    'field_1' => 'test',
                    'field_2' => 'test2',
                ],
            ]
        );

        // Asset autoloading.
        $this->assertEquals('тест', $translateModel->getTranslation('field_1', 'bg'));
        $this->assertEquals('тест2', $translateModel->getTranslation('field_2', 'bg'));

        $translateModel->setTranslation('field_1', 'bg', 'тест100');

        // Assert that we override translated attribute values before save
        $this->assertEquals('тест100', $translateModel->getTranslation('field_1', 'bg'));

        $translateModel->save();

        $this->assertEquals('тест100', $translateModel->getTranslation('field_1', 'bg'));
        $this->assertEquals('тест2', $translateModel->getTranslation('field_2', 'bg'));

        $translateModel->refreshTranslations();
        $this->assertEquals('тест100', $translateModel->getTranslation('field_1', 'bg'));
        $this->assertEquals('тест2', $translateModel->getTranslation('field_2', 'bg'));
    }

    /**
     *
     */
    public function testQuery()
    {
        $translateModel = new TranslateModel();

        $translateModel->not_translatable = 'Test';

        $translateModel->field_1 = 'test';
        $translateModel->field_2 = 'test2';

        $translateModel->setTranslation('field_1', 'bg', 'тест');
        $translateModel->setTranslation('field_2', 'bg', 'тест2');

        $translateModel->save();

        // Todo we need to find a way to load only required fields
        $all = TranslateModel::where($translateModel->getQualifiedKeyName(),
            $translateModel->getKey())->get('not_translatable');

        $this->assertEquals(1, $all->count());

        $this->assertEquals('test', $translateModel->field_1);
        $this->assertEquals('test2', $translateModel->field_2);

        $translateModel->fresh();

        // Check if we switch locales
        $this->app->setLocale('bg');
        $this->assertEquals('тест', $translateModel->field_1);
        $this->assertEquals('тест2', $translateModel->field_2);

    }

}