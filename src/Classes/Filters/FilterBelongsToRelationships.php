<?php

namespace JamesDordoy\LaravelVueDatatable\Classes\Filters;

use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException;

class FilterBelongsToRelationships
{
    public function __invoke($query, $searchValue, $relationshipModelFactory, $model, $relationships)
    {
        $searchTerm = config('laravel-vue-datatables.models.search_term');

        if (isset($relationships['belongsTo'])) {

            foreach ($relationships['belongsTo'] as $tableName => $options) {

                if (! isset($options['model']) || !isset($options['columns'])) {
                    return $query;
                }

                $model = $relationshipModelFactory($options['model'], $tableName);

                $query->orWhereHas($tableName, function ($query) use ($searchValue, $model, $options, $searchTerm) {
                    
                    if (isset($options['columns'])) {
                        
                        $tableName = $model->getTable();

                        foreach ($options['columns'] as $columnName => $col) {
                            if ($col[$searchTerm]) {
                                if ($columnName === key($options['columns'])) {
                                    $query->where("$tableName.$columnName", "like",  "%$searchValue%");
                                } else {
                                    $query->orWhere("$tableName.$columnName", "like",  "%$searchValue%");
                                }
                            }
                        }
                    } 
                });
            }

            return $query;
        }

        return $query;
    }
}