<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return $query;
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
        $query->orderBy($sortBy, in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc');
    }

    protected function applyGrouping(Builder $query, Request $request): void
    {
        if (!$request->filled('group_by')) return;

        $fields = explode(',', $request->input('group_by'));
        $query->select(array_merge($fields, [DB::raw('COUNT(*) as total')]))
              ->groupBy($fields);

        if ($request->has('having')) {
            $havings = json_decode($request->input('having'), true);
            foreach ($havings as $h) {
                $query->having($h['column'], $h['operator'], $h['value']);
            }
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
