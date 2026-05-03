<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Http\Requests\Api\ApiIndexRequest;
use Illuminate\Database\Eloquent\Builder;

trait AppliesApiFilters
{
    /**
     * @param array<int, string> $columns
     */
    protected function applySearch(Builder $query, ApiIndexRequest $request, array $columns): Builder
    {
        $search = trim((string) $request->query('search', ''));

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($columns, $search): void {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%'.$search.'%');
            }
        });
    }

    protected function applyStatus(Builder $query, ApiIndexRequest $request, string $column = 'status'): Builder
    {
        $status = trim((string) $request->query('status', ''));

        if ($status !== '') {
            $query->where($column, $status);
        }

        return $query;
    }

    /**
     * @param array<int, string> $allowed
     */
    protected function applySort(Builder $query, ApiIndexRequest $request, array $allowed = ['created_at', 'updated_at']): Builder
    {
        $sort = (string) $request->query('sort', '-created_at');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        if (! in_array($column, $allowed, true)) {
            $column = 'created_at';
            $direction = 'desc';
        }

        return $query->orderBy($column, $direction);
    }
}
