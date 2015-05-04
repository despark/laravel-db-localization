<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Support\Facades\Config;

trait i18nModelTrait
{
    /**
     * The current translation.
     */
    protected $translation;

    /**
     * Setup a one-to-many relation.
     *
     * @return mixed
     */
    public function translations()
    {
        return $this->hasMany($this->translator);
    }

    /**
     * Get translator field value.
     *
     *
     * @return translator field
     */
    public function getLocaleField()
    {
        return $this->localeField;
    }

    /**
     * Get translator field value.
     *
     *
     * @return translated attributes
     */
    public function getTranslatorField()
    {
        return $this->translatedAttributes;
    }

    /**
     * Get translator field value.
     *
     *
     * @return translated attributes
     */
    public function getTranslatedAttributes()
    {
        return $this->translatorField;
    }

    /**
     * Get administration locale id.
     *
     * @param null $locale
     *
     * @return locale id
     */
    public function getI18nId($locale = null)
    {
        if (!$locale) {
            $locale = \App::getLocale();
        }
        $localeModel =  Config::get('laravel-db-localization::locale_class');
        $i18n = $localeModel::select('id')->where('locale', $locale)->firstOrFail();

        $this->i18nId = $i18n->id;

        return $this->i18nId;
    }

    /**
     * Get specific translation.
     *
     * @param null $locale
     */
    public function translate($locale = null)
    {
        $translationModel  = new $this->translator();

        if (!is_int($locale)) {
            $locale = $this->getI18nId($locale);
        }
        $trans = $translationModel::where($this->translatorField, $this->id)
            ->where($this->localeField, $locale)->first();

        return $trans;
    }

    /**
     * Create new record.
     *
     * @param array $attributes
     */
    public static function create(array $attributes)
    {
        $model = new static($attributes);

        $model->save($attributes);
    }

    /**
     * Save record.
     *
     * @param array $options
     */
    public function save(array $options = [])
    {
        if (empty($options)) {
            $options = \Input::all();
        }
        // if method is put or patch remove translations
        if (\Request::method() === 'PUT' || \Request::method() === 'PATCH') {
            $this->deleteTranslations($this->id);
        }

        $translatableId = $this->saveTranslatable($options);
        $this->saveTranslations($translatableId, $options);
    }

    /**
     * Save untranslatable falues.
     *
     * @param array $options
     *
     * @return translatable Id
     */
    public function saveTranslatable($options)
    {
        parent::save($options);

        $translatableId = $this->id;

        return $translatableId;
    }

    /**
     * Insert translation values.
     *
     * @param array translatable Id
     * @param array $options
     *
     * @return id
     */
    public function saveTranslations($translatableId, $options)
    {
        $translationsArray = [];
        $explode = [];

        $fillables = $this->translatedAttributes;
        foreach ($options as $input) {
            if (is_array($input)) {
                foreach ($input as $i18n => $i18nValue) {
                    if ($i18nValue) {
                        $explode = explode('_', $i18n);
                        $i18nId = array_last($explode, function ($first, $last) {
                            return $last;
                        });
                        $filedName = str_replace('_'.$i18nId, '', $i18n);
                        if (in_array($filedName, $fillables)) {
                            $translationsArray[$i18nId][$filedName] = $i18nValue;
                            $translationsArray[$i18nId][$this->localeField] = $i18nId;
                            $translationsArray[$i18nId][$this->translatorField] = $translatableId;
                        }
                    }
                }
            }
        }
        $translationModel  = new $this->translator();

        foreach ($translationsArray as  $translationValues) {
            $translationModel->insert($translationValues);
        }
    }

    /**
     * Delete all translations.
     *
     * @param array translatable Id
     */
    public function deleteTranslations($translatableId)
    {
        $translationModel  = new $this->translator();

        $translationModel::where($this->translatorField, $translatableId)->delete();
    }
}
