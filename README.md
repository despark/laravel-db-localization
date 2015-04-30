# Laravel DB Localization

## Installation

Open `composer.json` file of your project and add the following to the require array:
```json
"despark/laravel-db-localization": "1.0.0"
```

Now run `composer update` to install the new requirement.

Once it's installed, you need to register the service provider in `app/config/app.php` in the providers array:
```php
'providers' => array(
  ...
  'Despark\LaravelDbLocalization\LaravelDbLocalizationServiceProvider',
);
```

Publish the config file:
`php artisan config:publish despark/laravel-db-localization`

# How to use it


## Database Example

- First you need to create your languages table

```php
Schema::create('i18n', function (Blueprint $table) {
        $table->increments('id');
        $table->string('locale')->unique()->index();
        $table->string('name')->index();
        $table->timestamps();
});
```
- Example of translatable table

```php
Schema::create('contacts', function (Blueprint $table) {
        $table->increments('id');

        // untranslatable columns
        $table->string('fax');
        $table->string('phone');
        $table->timestamps();
});
```
- Example of translations table

```php
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
```
## Model Example
```php
class Contacts extends Eloquent
{
    use i18nModelTrait;  // You must use i18nModelTrait

    protected $fillable = [
        'fax',
        'phone',
    ];

    protected $translator = 'Despark\LaravelDbLocalization\ContactsI18n'; // Here you need to add your translations table model name

    protected $translator_field = 'contact_id'; // your translator field name

    protected $locale_field = 'i18n_id'; // here is your locale field name

    protected $translatedAttributes = ['contact_id', 'i18n_id', 'name', 'location']; // translatable fillables
}

class ContactsI18n extends Eloquent
{
    protected $table = 'contacts_i18n';
}
```
## View example

```php
{{ Form::text("fax", null) }}
{{ Form::text("phone", null) }}

@foreach($languages as $language)
    {{ Form::text("name[name_$language->id]", null) }}  // Follow this convention array( fieldname_languageId );
    {{ Form::text("location[location_$language->id]", null) }}
@endforeach
```

## Config Example
```php
app/config/packages/despark/laravel-db-localization/config.php
    'locale_class' => 'Despark\LaravelDbLocalization\I18n',
```

## If you want to checkout our example you need to follow this commands:

Execute migrations with the following command
`php artisan migrate --package="despark/laravel-db-localization"`

This will create tables `i18n`, `contacts`, `contacts_i18n`.

Now you must seed `i18n` table:
`php artisan db:seed --class="Despark\LaravelDbLocalization\DatabaseSeeder"`

Now you can check how it works:
 http://youtdomain.name/localization_example/

