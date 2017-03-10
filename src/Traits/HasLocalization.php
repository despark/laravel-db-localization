<?php


namespace Despark\LaravelDbLocalization\Traits;


use Despark\LaravelDbLocalization\Observers\ModelObserver;

/**
 * Class HasLocalization.
 */
trait HasLocalization
{
    /**
     * @var array
     */
    protected $translatedAttributes = [];

    /**
     * @var array
     */
    protected $translatedAttributesOriginal = [];

    /**
     * @var string
     */
    protected $currentLocale;

    /**
     *
     */
    public static function bootHasLocalization()
    {
        static::observe(ModelObserver::class);
    }

    /**
     * @param string $key
     * @return null
     */
    protected function getAttributeFromArray($key)
    {
        $localizedValue = $this->getTranslation($key, $this->getCurrentLocale());

        if (! is_null($localizedValue)) {
            return $localizedValue;
        }

        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    /**
     * @param $key
     * @param $locale
     * @param $value
     */
    public function setTranslation($key, $locale, $value)
    {
        if (! $this->isGuarded($key)) {
            $this->translatedAttributes[$locale][$key] = $value;
        }
    }

    /**
     * @param $key
     * @param $locale
     * @return null
     */
    public function getTranslation($key, $locale)
    {
        return $this->translatedAttributes[$locale][$key] ?? null;
    }

    /**
     * @return array
     */
    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes;
    }

    /**
     * @return string
     */
    protected function getCurrentLocale()
    {
        if (! isset($this->currentLocale)) {
            $this->currentLocale = \App::getLocale();
        }

        return $this->currentLocale;
    }

    /**
     * @return string
     */
    public function getTranslationTable()
    {
        return $this->getTable().'_i18n';
    }
}