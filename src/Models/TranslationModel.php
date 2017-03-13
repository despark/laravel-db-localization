<?php


namespace Despark\LaravelDbLocalization\Models;


use Despark\LaravelDbLocalization\Contracts\Translatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DbLocalizationModel.
 */
class TranslationModel extends Model
{

    /**
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * @var Translatable
     */
    protected $translatableModel;

    /**
     * @return Translatable
     */
    public function getTranslatableModel(): Translatable
    {
        return $this->translatableModel;
    }

    /**
     * @param Translatable $translatableModel
     * @return $this
     */
    public function setTranslatableModel(Translatable $translatableModel)
    {
        $this->translatableModel = $translatableModel;
        $this->setTable($translatableModel->getTranslationTable());

        return $this;
    }

    /**
     * @return Collection
     */
    public function loadTranslations()
    {
        return $this->where('parent_id', $this->getTranslatableModel()->getKey())
                    ->get();
    }


    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (isset($options['direct_save'])) {
            return parent::save($options);
        }

        // Get all records for this translation model
        /** @var Collection $collection */
        $collection = $this->loadTranslations();


        $translatedAttributes = $this->getTranslatableModel()->getTranslatedAttributes();

        $availableLocales = array_intersect($this->getTranslatableModel()->getAvailableLocales(),
            array_keys($collection->groupBy('locale')->toArray()));


        // Update already existing
        foreach ($availableLocales as $locale) {
            /** @var TranslationModel $item */
            $item = $collection->first(function ($item) use ($locale) { return $item->locale === $locale; });
            $item->setTable($this->getTable());

            foreach ($translatedAttributes[$locale] as $attributeName => $value) {
                $item->setAttribute($attributeName, $value);
            }
            $item->save(['direct_save' => 1]);
            unset($translatedAttributes[$locale]);
        }

        // Create new
        foreach ($translatedAttributes as $locale => $values) {
            $attributes = [
                'parent_id' => $this->getTranslatableModel()->getKey(),
                'locale' => $locale,
            ];
            $attributes = array_merge($attributes, $values);
            $instance = new self();
            $instance->setTable($this->getTable());

            $instance->setRawAttributes($attributes);
            $instance->save(['direct_save' => 1]);
        }

        //        // We need to update translatable model
        //        $this->getTranslatableModel()->refreshTranslations();
    }

}