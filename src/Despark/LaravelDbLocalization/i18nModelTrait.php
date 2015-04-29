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
     * Get administration locale id.
     *
     * @param null $locale
     *
     * @return locale id
     */
    public function getI18nId($locale = null)
    {
        if (!$locale) {
            $locale = Config::get('app.locale');
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
        $localeId = $this->getI18nId($locale);
        $trans = $translationModel::where($this->translator_field, $this->id)
            ->where($this->locale_field, $localeId)->first();

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
                        $filedName = array_get($explode, 0);
                        $i18nId = array_get($explode, 1);

                        if (in_array($filedName, $fillables)) {
                            $translationsArray[$i18nId][$filedName] = $i18nValue;
                            $translationsArray[$i18nId][$this->locale_field] = $i18nId;
                            $translationsArray[$i18nId][$this->translator_field] = $translatableId;
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

        $translationModel::where($this->translator_field, $translatableId)->delete();
    }
}
