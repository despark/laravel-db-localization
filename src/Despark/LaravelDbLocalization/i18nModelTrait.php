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
     * Get translator model name.
     *
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
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
        return $this->translatorField;
    }

    /**
     * Get translator field value.
     *
     *
     * @return translated attributes
     */
    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes;
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
        $localeModel = Config::get('laravel-db-localization::locale_class');
        $i18n = $localeModel::select('id')->where('locale', $locale)->first();

        $i18nId = null;

        if (isset($i18n->id)) {
            $i18nId = $i18n->id;
        }

        return $i18nId;
    }

    /**
     * Get specific translation.
     *
     * @param false $locale
     */
    public function translate($locale = false, $alowRevision = false)
    {
        $translation = null;
        $translationModel  = new $this->translator();

        if (!is_int($locale)) {
            $locale = $this->getI18nId($locale);
        }

        if (isset($this->id) && $locale) {
            $translation = $translationModel::where($this->translatorField, $this->id)
                ->where($this->localeField, $locale)->first();
        }

        if ($alowRevision == true) {
            if (isset($translation->show_revision)) {
                if ($translation->show_revision == 1) {
                    $translation = $translation->setAttributeNames(unserialize($translation->revision));
                }
            }
        }

        return $translation;
    }

    public function scopeWithTranslations($query, $locale = null, $softDelete = null)
    {
        // get i18n id by locale
        $i18nId = $this->getI18nId($locale);

        $translatorTable = new $this->translator();
        $translatorTableName = $translatorTable->getTable();
        $translatableTable = $this->getTable();

        $translatorField = $this->getTranslatorField();
        $localeField = $this->getLocaleField();

        if (! $locale) {
            $query = $query->leftJoin(
            $translatorTableName,
            $translatorTableName.'.'.$translatorField, '=', $translatableTable.'.id');
        } else {
            $aliasSoftDelete = '';
            if ($softDelete) {
                $aliasSoftDelete = 'AND translatorAlias.deleted_at is null ';
            }

            $query = $query->leftJoin(\DB::raw(
            '( SELECT
                    translatorAlias.*
                FROM '.$translatorTableName.' as translatorAlias
                WHERE translatorAlias.'.$localeField.' = '.$i18nId.'
                '.$aliasSoftDelete.'
             ) as '.$translatorTableName
            ), function ($join) use ($translatorTableName, $translatorField, $translatableTable) {
                $join->on($translatorTableName.'.'.$translatorField, '=', $translatableTable.'.id');
            });
        }

        if ($softDelete) {
            $query = $query->whereNULL($translatorTableName.'.deleted_at');
        }

        return $query;
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

        $this->saveTranslations($this->id, $options);

        return $this;
    }

    /**
     * Insert translation values.
     *
     * @param array translatable Id
     * @param array $options
     */
    public function saveTranslations($translatableId, $options)
    {
        $translationsArray = [];
        $explode = [];

        $fillables = $this->translatedAttributes;

        foreach ($options as $input) {
            if (is_array($input)) {
                foreach ($input as $i18n => $i18nValue) {
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

        $modelName = $this->translator;

        foreach ($translationsArray as  $translationValues) {
            $translation = $modelName::where($this->translatorField, array_get($translationValues, $this->translatorField))
                ->where($this->localeField, array_get($translationValues, $this->localeField))
                ->first();

            // check if translation exists with same values
            $query = $modelName::select('id');
            foreach ($translationValues as $key => $val) {
                $query->where($key, $val);
            }
            $result = $query->first();

            if (!isset($result->id)) {
                if (! isset($translation->id)) {
                    $translation = new $modelName();
                }

                foreach ($translationValues as $key => $val) {
                    $translation->$key = $val;
                }

                $translation->save();
            }
        }
    }
}
