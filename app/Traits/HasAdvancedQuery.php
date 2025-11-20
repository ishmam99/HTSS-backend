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


    protected function applyFilters(Builder $query, Request $request): void
    {
        foreach ($request->all() as $key => $value) {
            if (in_array($key, [
                'search', 'sort_by', 'sort_order', 'page', 'per_page',
                'with', 'group_by', 'group_select', 'where', 'or_where'
            ])) continue;

            if (is_array($value)) $query->whereIn($key, $value);
            else $query->where($key, $value);
        }
    }

    protected function applyDateFilters(Builder $query, Request $request): void
    {
        foreach (['created_at', 'updated_at', 'published_at'] as $col) {
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

    $model = $query->getModel();
    $parentTable = $model->getTable();

    $groupFields = explode(',', $request->input('group_by'));

    foreach ($groupFields as $field) {

        // ─────────────────────────────────────────────
        // CASE 1: Simple column
        // ─────────────────────────────────────────────
        if (!str_contains($field, '.')) {
            $query->selectRaw("$parentTable.$field");
            $query->groupBy("$parentTable.$field");
            continue;
        }

        // ─────────────────────────────────────────────
        // CASE 2: relation.column or relation.pivotColumn
        // ─────────────────────────────────────────────
        [$relationName, $column] = explode('.', $field);

        if (!method_exists($model, $relationName)) {
            continue; // skip invalid relations
        }

        $relation = $model->$relationName();
        $relatedTable = $relation->getRelated()->getTable();

        // ─────────────────────────────────────────────
        // HANDLE MANY-TO-MANY
        // ─────────────────────────────────────────────
        if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {

            $pivot = $relation->getTable(); // pivot table name
            $foreignKey = $relation->getForeignPivotKeyName(); // pivot.user_id
            $relatedKey = $relation->getRelatedPivotKeyName(); // pivot.software_id

            // Join pivot table
            $query->leftJoin($pivot, "$pivot.$foreignKey", '=', "$parentTable.id");

            // Join related table
            $query->leftJoin($relatedTable, "$relatedTable.id", '=', "$pivot.$relatedKey");

            // If the column exists in pivot
            if (Schema::hasColumn($pivot, $column)) {
                $query->selectRaw("$pivot.$column");
                $query->groupBy("$pivot.$column");
            }
            // or in related table
            elseif (Schema::hasColumn($relatedTable, $column)) {
                $query->selectRaw("$relatedTable.$column");
                $query->groupBy("$relatedTable.$column");
            }

            continue;
        }

        // ─────────────────────────────────────────────
        // HANDLE BELONGS-TO / HAS-ONE / HAS-MANY
        // ─────────────────────────────────────────────
        $parentKey = $relation->getQualifiedForeignKeyName();  // users.profile_id
        $ownerKey  = $relation->getQualifiedOwnerKeyName();    // profiles.id

        $query->leftJoin($relatedTable, $ownerKey, '=', $parentKey);

        $query->selectRaw("$relatedTable.$column");
        $query->groupBy("$relatedTable.$column");
    }
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
