<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Contacts extends Eloquent
{
    use i18nModelTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'fax',
        'phone',
    ];

    /**
     * Localization model name.
     *
     * @var string
     */
    protected $translator = 'Despark\LaravelDbLocalization\ContactsI18n';

    /**
     * Localization field name.
     *
     * @var string
     */
    protected $translatorField = 'contact_id';

    /**
     * Localization locale field name.
     *
     * @var string
     */
    protected $localeField = 'i18n_id';

    /**
     * Localization fillables.
     *
     * @var array
     */
    protected $translatedAttributes = ['contact_id', 'i18n_id', 'name', 'location'];
}
