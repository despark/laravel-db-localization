<?php


namespace Despark\LaravelDbLocalization\Contracts;


/**
 * Interface Translatable
 * @package Despark\LaravelDbLocalization\Contracts
 */
interface Translatable
{

    /**
     * @param string $key
     * @param string $locale
     * @param mixed  $value
     * @return string
     */
    public function setTranslation($key, $locale, $value);

    /**
     * @param string      $locale
     * @param null|string $key
     * @return mixed
     */
    public function unsetTranslation($locale, $key = null);

    /**
     * @param string $key
     * @param string $locale
     * @return string
     */
    public function getTranslation($key, $locale = null);

    /**
     * @return string
     */
    public function getTranslationTable();

    /**
     * @return array
     */
    public function getTranslatedAttributes();

    /**
     * @return array
     */
    public function getAvailableLocales();

    /**
     * @return mixed
     */
    public function refreshTranslations();

    /**
     * @return mixed
     */
    public function getDefaultLocale();

}