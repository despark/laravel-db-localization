<?php


namespace Despark\LaravelDbLocalization\Observers;


use Despark\LaravelDbLocalization\Contracts\Translatable;
use Despark\LaravelDbLocalization\Models\TranslationModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelObserver.
 */
class ModelObserver
{
    /**
     * @var TranslationModel
     */
    protected $localizationModel;

    /**
     * ModelObserver constructor.
     * @param TranslationModel $localizationModel
     */
    function __construct(TranslationModel $localizationModel)
    {
        $this->localizationModel = $localizationModel;
    }

    public function saving(Translatable $model)
    {
        // We need to clear all translatable from model
        foreach ($model->getAttributes() as $attributeName => $value) {
            if ($model->isTranslatable($attributeName)) {
                unset($model->{$attributeName});
            }
        }
    }

    /**
     * @param Model|Translatable $model
     */
    public function saved(Translatable $model)
    {
        $this->localizationModel->setTable($model->getTranslationTable())
                                ->setTranslatableModel($model)
                                ->save();
    }
}