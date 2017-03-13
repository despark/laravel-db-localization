<?php


namespace Despark\LaravelDbLocalization\Scopes;


use Despark\LaravelDbLocalization\Contracts\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\JoinClause;

class TranslationScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($model instanceof Translatable) {
            $table = $model->getTranslationTable().' as translation_table';
            if (! $this->isJoined($builder, $table)) {

                $builder->leftJoin($table, function ($q) use ($model) {
                    $q->on('translation_table.parent_id', '=', $model->getQualifiedKeyName())
                      ->where('translation_table.locale', \App::getLocale());

                });

                foreach ($model->getTranslatable() as $attribute) {
                    $builder->addSelect('translation_table.'.$attribute);
                }
            }
        }
    }

    /**
     * @param Builder $builder
     * @param         $table
     * @return bool
     */
    protected function isJoined(Builder $builder, $table)
    {
        $joins = $builder->getQuery()->joins ?? [];
        /** @var JoinClause $join */
        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }
    }
}