<?php


namespace Despark\LaravelDbLocalization\Observers;


use Despark\LaravelDbLocalization\Models\DbLocalizationModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelObserver.
 */
class ModelObserver
{
    /**
     * @var DbLocalizationModel
     */
    protected $localizationModel;

    /**
     * ModelObserver constructor.
     * @param DbLocalizationModel $localizationModel
     */
    function __construct(DbLocalizationModel $localizationModel)
    {
        $this->localizationModel = $localizationModel;
    }

    /**
     * @param Model $model
     */
    public function saved(Model $model)
    {

        $this->localizationModel->setTable($model->getTranslationTable())
                                ->setRawAttributes($model->getTranslatedAttributes())
                                ->save();
    }
}