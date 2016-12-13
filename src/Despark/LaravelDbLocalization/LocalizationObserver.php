<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LocalizationObserver.
 */
class LocalizationObserver
{
    /**
     * @param Model $model
     */
    public function saved(Model $model)
    {
        $model->saveTranslations($model->id);
    }
}
