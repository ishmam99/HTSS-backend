<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HasAdvancedQuery
{
    /**
     * Bootable entrypoint — returns a prepared query builder
     */
    public static function advancedQuery(Request $request): Builder
    {
        $instance = new static();
        $query = static::query();

        $instance->applyRelationships($query, $request);
        $instance->applySearch($query, $request);
       
        $instance->applyFilters($query, $request);
        $instance->applyDateFilters($query, $request);
        $instance->applyRangeFilters($query, $request);
        $instance->applySorting($query, $request);
        $instance->applyGrouping($query, $request);
        $instance->applyCustomConditions($query, $request);
        $instance->applyPluck($query, $request);
        return $query;
    }
    protected function applyPluck(Builder $query, Request $request)
{
    if (!$request->filled('pluck')) {
        return;
    }

    // Simple pluck: ?pluck=name
    // Or key-value: ?pluck=name,id
    $columns = explode(',', $request->input('pluck'));

    if (count($columns) === 1) {
        $query->select($columns[0]);
    } else {
        // Laravel pluck requires key, value format
        $query->select([$columns[0], $columns[1]]);
    }
}


  protected function applyRelationships(Builder $query, Request $request): void
{
    if ($request->filled('with')) {
        $relations = explode(',', $request->input('with'));

        // Optional: if your model defines allowed relations (e.g. $allowedWith)
        if (property_exists($this, 'allowedWith')) {
            $relations = array_intersect($relations, $this->allowedWith);
        }

        $query->with($relations);
    }
}


   protected function applySearch(Builder $query, Request $request): void
{
    if (!$request->filled('search') || !property_exists($this, 'searchable')) return;

    $term = $request->input('search');
    $fields = $this->searchable ?? [];

    $query->where(function ($q) use ($fields, $term) {
        foreach ($fields as $field) {
            if (str_contains($field, '.')) {
                // Handle relation search: e.g. user.name
                [$relation, $column] = explode('.', $field, 2);
                $q->orWhereHas($relation, function ($relQ) use ($column, $term) {
                    $relQ->where($column, 'LIKE', "%{$term}%");
                });
            } else {
                // Regular field
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        }
    });
}


//   protected function applyFilters(Builder $query, Request $request): void
//     {
//         $model = $query->getModel();
//         $parentTable = $model->getTable();

//         // Existing filters
//         foreach ($request->all() as $key => $value) {
//             if (in_array($key, [
//                 'search', 'sort_by', 'sort_order', 'page', 'per_page','pluck',
//                 'with', 'group_by', 'group_select', 'where', 'or_where',
//                 'relation', 'relation_field', 'relation_value', 'filter','created_at_from','created_at_to', 'updated_at_to','updated_at_from', 'published_at' , 'date_from','date_to'
//             ])) continue;

//             if (is_array($value)) $query->whereIn($key, $value);
//             else $query->where($key, $value);
//         }

//         // --------------------------
//         // DYNAMIC MANY-TO-MANY FILTER
//         // --------------------------
//         $relation      = $request->input('relation');        // e.g., 'softwares'
//         $relationField = $request->input('relation_field');  // e.g., 'id'
//         $relationValue = $request->input('relation_value');  // e.g., 1

//         // if ($relation && $relationField && $relationValue && method_exists($model, $relation)) {
//         //     $query->whereHas($relation, function ($q) use ($relationField, $relationValue) {
//         //         $q->where($relationField, $relationValue);
//         //     });
//         // }

 

//     if ($relation && $relationField && $relationValue) {
//         // relation can be: customer OR customer.company
//         $query->whereHas($relation, function ($q) use ($relationField, $relationValue) {
//             $q->where($relationField, $relationValue);
//         });
//     }
//     }
    protected function applyFilters(Builder $query, Request $request): void
{
    $model = $query->getModel();

    /*
    |--------------------------------------------------------------------------
    | 1) SIMPLE COLUMN FILTERS
    |--------------------------------------------------------------------------
    */
    foreach ($request->all() as $key => $value) {

        if (in_array($key, [
            'search','sort_by','sort_order','page','per_page','pluck',
            'with','group_by','group_select',
            'where','or_where',
            'relation','relation_field','relation_value',
            'created_at_from','created_at_to',
            'updated_at_from','updated_at_to',
            'published_at_from','published_at_to',
            'date_from','date_to'
        ])) {
            continue;
        }

        if (is_array($value)) {
            $query->whereIn($key, $value);
        } else {
            $query->where($key, $value);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 2) DYNAMIC RELATION FILTER (customer, customer.company, etc)
    |--------------------------------------------------------------------------
    */
    $relation      = $request->input('relation');        
    $relationField = $request->input('relation_field');  
    $relationValue = $request->input('relation_value');  

    if (!empty($relation) && !empty($relationField) && !is_null($relationValue)) {

        $query->whereHas($relation, function ($q) use ($relationField, $relationValue) {

            if (is_array($relationValue)) {
                $q->whereIn($relationField, $relationValue);
            } else {
                $q->where($relationField, '=', $relationValue);
            }

        });
    }
}

    protected function applyDateFilters(Builder $query, Request $request): void
    {
        foreach (['created_at', 'updated_at', 'published_at' , 'date'] as $col) {
            if ($request->has("{$col}_from")) {
                $query->whereDate($col, '>=', $request->input("{$col}_from"));
            }
            if ($request->has("{$col}_to")) {
                $query->whereDate($col, '<=', $request->input("{$col}_to"));
            }
        }
    }

    protected function applyRangeFilters(Builder $query, Request $request): void
    {
        foreach (['price', 'views', 'rating'] as $col) {
            if ($request->has("{$col}_min")) {
                $query->where($col, '>=', $request->input("{$col}_min"));
            }
            if ($request->has("{$col}_max")) {
                $query->where($col, '<=', $request->input("{$col}_max"));
            }
        }
    }

   protected function applySorting(Builder $query, Request $request): void
{
    $sortBy = $request->input('sort_by', 'id');
    $sortOrder = $request->input('sort_order', 'desc');

    // Skip sorting by columns not in GROUP BY if grouping
    if ($request->filled('group_by')) {
        $groupFields = explode(',', $request->input('group_by'));
        if (!in_array($sortBy, $groupFields)) {
            return; // ignore invalid sort
        }
    }

    $query->orderBy($sortBy, in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc');
}

protected function applyGrouping(Builder $query, Request $request): void
{
    if (!$request->filled('group_by')) return;

    $model       = $query->getModel();
    $parentTable = $model->getTable();
    $groupFields = explode(',', $request->input('group_by'));

    $selects = [];
    $groupBys = [];

    foreach ($groupFields as $field) {

        // CASE 1: relation.column  (including pivot)
        if (str_contains($field, '.')) {

            [$relation, $column] = explode('.', $field);

            if (!method_exists($model, $relation)) {
                continue; // invalid relation
            }

            $relationObj = $model->$relation();
            $relatedTable = $relationObj->getRelated()->getTable();

            // belongsToMany → pivot grouping supported
            if ($relationObj instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                $pivot = $relationObj->getTable(); // pivot table name

                // join pivot
                $query->leftJoin($pivot, "$pivot.{$relationObj->getForeignPivotKeyName()}", '=', "$parentTable.id");

                // join related table
                $query->leftJoin($relatedTable, "$relatedTable.id", '=', "$pivot.{$relationObj->getRelatedPivotKeyName()}");

                $selects[] = "$relatedTable.$column";
                $groupBys[] = "$relatedTable.$column";
            }

            // belongsTo / hasOne / hasMany
            else {
                $foreignKey = $relationObj->getForeignKeyName();
                $localKey   = $relationObj->getOwnerKeyName();

                $query->leftJoin($relatedTable, "$relatedTable.$localKey", '=', "$parentTable.$foreignKey");

                $selects[] = "$relatedTable.$column";
                $groupBys[] = "$relatedTable.$column";
            }
        }

        // CASE 2: simple column on parent table
        else {
            $selects[] = "$parentTable.$field";
            $groupBys[] = "$parentTable.$field";
        }
    }

    // ADD COUNT (this is what was missing)
    $query->select(array_merge($selects, [
        DB::raw('COUNT(*) AS total')
    ]));

    $query->groupBy($groupBys);
}



    protected function applyCustomConditions(Builder $query, Request $request): void
    {
        if ($request->has('where')) {

            foreach (json_decode($request->input('where'), true) as $condition) {
                $query->where($condition['column'], $condition['operator'], $condition['value']);
            }
        }

        if ($request->has('or_where')) {
            $query->where(function ($q) use ($request) {
                foreach (json_decode($request->input('or_where'), true) as $condition) {
                    $q->orWhere($condition['column'], $condition['operator'], $condition['value']);
                }
            });
        }
    }
}
