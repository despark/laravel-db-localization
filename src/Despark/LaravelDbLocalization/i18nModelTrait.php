<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Support\Facades\Config;
use App;

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
            $locale = App::getLocale();
        }

        $localeModel = Config::get('laravel-db-localization::locale_class');
        $i18n = App::make($localeModel)->select('id')->where('locale', $locale)->first();

        if (isset($i18n->id)) {
            return $i18n->id;
        }
    }

    /**
     * Get specific translation.
     *
     * @param false $locale
     */
    public function translate($locale = null, $alowRevision = false)
    {
        $translation = null;
        if (!is_int($locale)) {
            $locale = $this->getI18nId($locale);
        }

        if (isset($this->id) && $locale) {
            $translation = App::make($this->translator)->where($this->translatorField, $this->id)
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
        $translatorTableName = App::make($this->translator)->getTable();
        $translatableTable = $this->getTable();
        $translatorField = $this->getTranslatorField();
        $localeField = $this->getLocaleField();
        if (!$locale) {
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
      * Insert translation values.
      *
      * @param array translatable Id
      * @param array $options
      */
     public function saveTranslations($translatableId)
     {
         $translationsArray = [];
         foreach (\Request::all() as $input) {
             if (is_array($input)) {
                 foreach ($input as $i18n => $i18nValue) {
                     $i18nId = array_last(explode('_', $i18n), function ($first, $last) {
                         return $last;
                     });
                     $filedName = str_replace('_'.$i18nId, '', $i18n);
                     if (in_array($filedName, $this->translatedAttributes)) {
                         $translationsArray[$i18nId][$filedName] = $i18nValue;
                         $translationsArray[$i18nId][$this->localeField] = $i18nId;
                         $translationsArray[$i18nId][$this->translatorField] = $translatableId;
                     }
                 }
             }
         }

         foreach ($translationsArray as  $translationValues) {
             $translatorId = array_get($translationValues, $this->translatorField);
             $localeId = array_get($translationValues, $this->localeField);
             $translation = $this->translator->where($this->translatorField, $translatorId)
                 ->where($this->localeField, $localeId)
                 ->first();
             if (!$translation) {
                 $translation = App::make($this->translator);
             }

             $translation->fillable = array_keys($translationValues);
             $translation->fill($translationValues);
             $translation->save();
         }
     }
}
