# Laravel DB Localization for laravel 5.1

## Installation

Open `composer.json` file of your project and add the following to the require array:
```json
"despark/laravel-db-localization": "2.0.*"
```

Now run `composer update` to install the new requirement.

Once it's installed, you need to register the service provider in `config/app.php` in the providers array:
```php
'providers' => array(
  ...
  Despark\LaravelDbLocalization\LaravelDbLocalizationServiceProvider::class,
);
```

Publish the config file:
`php artisan vendor:publish --provider="Despark\LaravelDbLocalization\LaravelDbLocalizationServiceProvider" --tag="config"`

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

use Despark\LaravelDbLocalization\i18nModelTrait;

class Contacts extends Eloquent
{
    use i18nModelTrait; // You must use i18nModelTrait

    protected $fillable = [
        'fax',
        'phone',
    ];

    protected $translator = 'Despark\LaravelDbLocalization\ContactsI18n'; // Here you need to add your translations table model name

    protected $translatorField = 'contact_id'; // your translator field name

    protected $localeField = 'i18n_id'; // here is your locale field name

    protected $translatedAttributes = ['contact_id', 'i18n_id', 'name', 'location']; // translatable fillables
}

class ContactsI18n extends Eloquent
{
    protected $table = 'contacts_i18n';
}
```
## View example

Create
```php
{!! Form::text("fax", null) !!}
{!! Form::text("phone", null) !!}

@foreach($languages as $language)
    {!! Form::text("name[name_$language->id]", null) !!}  // Follow this convention array( fieldname_languageId );
    {!! Form::text("location[location_$language->id]", null) !!}
@endforeach
```
Retrieve
```php
    // locale string
    $contacts->translate('en'); // all fields
    $contacts->translate('en')->location; // specific field

    // locale id
    $i18nId = 2;
    $contacts->translate($i18nId); // all fields
    $contacts->translate($i18nId)->location; // specific field
```

## Config Example
```php
config/laravel-db-localization.php
    'locale_class' => 'Despark\LaravelDbLocalization\I18n',
```


