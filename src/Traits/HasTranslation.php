<?php

namespace Despark\LaravelDbLocalization\Traits;

use Despark\LaravelDbLocalization\Models\TranslationModel;
use Despark\LaravelDbLocalization\Observers\ModelObserver;

/**
 * Class HasLocalization.
 */
trait HasTranslation
{
    /**
     * @var array
     */
    protected $translatedAttributes = [];

    /**
     * @var bool
     */
    protected $translationsLoaded = false;

    /**
     * @var TranslationModel
     */
    protected $translationModel;

    /**
     * @var string
     */
    protected $activeLocale;

    /**
     * Bootstrap the trait.
     */
    public static function bootHasTranslation()
    {
        static::observe(ModelObserver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasOne(TranslationModel::class, 'parent_id', 'id')->where('locale', \App::getLocale());
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);
        $model->setActiveLocale($this->getActiveLocale());

        return $model;
    }

    /**
     * Create a new model instance for a related model.
     * We overwrite this so we can dynamically create the table on the related model.
     *
     * @param string $class
     *
     * @return mixed
     */
    protected function newRelatedInstance($class)
    {
        if (is_a($class, TranslationModel::class, true)) {
            return tap(new $class(), function ($instance) {
                $instance->setTable($this->getTranslationTable());

                if (! $instance->getConnectionName()) {
                    $instance->setConnection($this->connection);
                }
            });
        }

        return tap(new $class(), function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    /**
     * @param string $key
     */
    public function getAttribute($key)
    {
        if (! $this->isTranslatable($key)) {
            return parent::getAttribute($key);
        }
        $localizedValue = $this->getTranslation($key, $this->getCurrentLocale());

        if (! is_null($localizedValue)) {
            return $localizedValue;
        }

        return parent::getAttributeFromArray($key);
    }

    /**
     * @param $key
     * @param $locale
     * @param $value
     */
    public function setTranslation($key, $locale, $value)
    {
        $this->translatedAttributes[$locale][$key] = $value;
    }

    /**
     * @param $key
     * @param $locale
     *
     * @return string|null
     */
    public function getTranslation($key, $locale = null)
    {
        if (is_null($locale)) {
            $locale = $this->getCurrentLocale();
        }
        // We need to load translations.
        $this->loadTranslations();

        if ($this->isTranslatable($key)) {
            return $this->translatedAttributes[$locale][$key] ?? null;
        }
    }

    /**
     * @param      $locale
     * @param null $key
     */
    public function unsetTranslation($locale, $key = null)
    {
        // unset the whole locale
        foreach ($this->translatedAttributes as $localeKey => $values) {
            if ($localeKey === $locale) {
                foreach ($values as $attributeKey => $value) {
                    if (is_null($key)) {
                        $this->translatedAttributes[$localeKey][$attributeKey] = null;
                    } else {
                        if ($attributeKey === $key) {
                            $this->translatedAttributes[$localeKey][$attributeKey] = null;
                        }
                    }
                }
                $value = null;
            }
        }
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslatable($key)) {
            $this->setTranslation($key, $this->getActiveLocale(), $value);

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Load transaltions from database.
     */
    public function loadTranslations()
    {
        if (! $this->translationsLoaded) {
            $collection = $this->getTranslationModel()
                               ->setTranslatableModel($this)
                               ->loadTranslations();

            foreach ($collection as $item) {
                foreach ($item->getAttributes() as $attributeName => $value) {
                    $this->translatedAttributes[$item->locale][$attributeName] = $value;
                }
            }

            $this->translationsLoaded = true;
        }
    }

    /**
     * @return array
     */
    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes;
    }

    /**
     * @return array
     */
    public function getAvailableLocales()
    {
        return array_keys($this->translatedAttributes);
    }

    /**
     * @return mixed
     */
    public function getDefaultLocale()
    {
        return config('app.fallback_locale');
    }

    /**
     * @return string
     */
    public function getActiveLocale(): string
    {
        if (! isset($this->activeLocale)) {
            $this->activeLocale = $this->getDefaultLocale();
        }

        return $this->activeLocale;
    }

    /**
     * @param string $activeLocale
     */
    public function setActiveLocale(string $activeLocale)
    {
        $this->activeLocale = $activeLocale;
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return \App::getLocale();
    }

    /**
     * @return string
     */
    public function getTranslationTable()
    {
        return $this->getTable().'_i18n';
    }

    /**
     * @return TranslationModel
     */
    public function getTranslationModel()
    {
        if (! isset($this->translationModel)) {
            $this->translationModel = (new TranslationModel())->setTranslatableModel($this);
        }

        return $this->translationModel;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function isTranslatable($key)
    {
        return in_array($key, $this->translatable);
    }

    public function refreshTranslations()
    {
        $this->translatedAttributes = [];
        $this->translationsLoaded = false;
        $this->loadTranslations();
    }

    /**
     * @return mixed
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }
}
