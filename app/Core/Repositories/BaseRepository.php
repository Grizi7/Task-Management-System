<?php

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected Builder $query;
    
    /**
     * Constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->query = $model->newQuery();
    }

    /**
     * Get the base query builder.
     *
     * @param array $relations
     * @return Builder
     */
    public function getQuery(array $relations = []): Builder
    {
        return $this->model->with($relations);
    }

    /**
     * Get all records.
     *
     * @param array $relations
     * @param array $columns
     * @param array $filters
     * @param array $withCount
     * @return Collection
     */
    public function all(array $relations = [], array $columns = ['*'], array $filters = [], array $withCount = []): Collection
    {
        $query = $this->getQuery($relations);
        $query = $this->applyFilters($query, $filters);
        return $query->withCount($withCount)->get($columns);
    }

    /**
     * Paginate records.
     *
     * @param int $perPage
     * @param array $relations
     * @param array $columns
     * @param array $filters
     * @param array $withCount
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $relations = [], array $columns = ['*'], array $filters = [], array $withCount = []): LengthAwarePaginator
    {
        $query = $this->getQuery($relations);
        $query = $this->applyFilters($query, $filters);
        return $query->withCount($withCount)->paginate($perPage, $columns);
    }

    /**
     * Find a record by ID.
     *
     * @param int|string $id
     * @param array $relations
     * @param array $columns
     * @return Model|null
     */
    public function find($id, array $relations = [], array $columns = ['*']): ?Model
    {
        return $this->getQuery($relations)->find($id, $columns);
    }

    /**
     * Find a record or fail.
     *
     * @param int|string $id
     * @param array $relations
     * @param array $columns
     * @return Model
     */
    public function findOrFail($id, array $relations = [], array $columns = ['*']): Model
    {
        return $this->getQuery($relations)->findOrFail($id, $columns);
    }

    /**
     * Find one by conditions.
     *
     * @param array $conditions
     * @param array $relations
     * @return Model|null
     */
    public function findOneBy(array $conditions, array $relations = []): ?Model
    {
        $query = $this->getQuery($relations);

        $query = $this->applyFilters($query, $conditions);

        return $query->first();
    }

    /**
     * Find by multiple conditions.
     *
     * @param array $conditions
     * @param array $relations
     * @return Collection
     */
    public function findBy(array $conditions, array $relations = []): Collection
    {
        $query = $this->getQuery($relations);

        $query = $this->applyFilters($query, $conditions);

        return $query->get();
    }


    /**
     * Create a new record.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        $filtered = $this->cleanUpAttributes($attributes);
        return $this->query->create($filtered);
    }

    /**
     * Update a record.
     *
     * @param Model $model
     * @param array $attributes
     * @return Model
     */
    public function update(Model $model, array $attributes): Model
    {
        $attributes = $this->cleanUpAttributes($attributes);
        return tap($model)->update($attributes)->fresh();
    }

    /**
     * Update a record by ID.
     *
     * @param int|string $id
     * @param array $attributes
     * @return Model
     */
    public function updateById($id, array $attributes): Model
    {
        $model = $this->findOrFail($id);
        $attributes = $this->cleanUpAttributes($attributes);
        return $this->update($model, $attributes);
    }

    /**
     * Update a record by conditions.
     *
     * @param array|string $conditions
     * @param mixed $value
     * @param array $attributes
     * @return Model|null
     */
    public function updateBy($conditions, $value = null, array $attributes = []): ?Model
    {
        // Normalize conditions
        if (is_string($conditions)) {
            $conditions = [$conditions => $value];
        }

        $query = $this->getQuery();

        $query = $this->applyConditions($query, $conditions);

        $model = $query->first();

        if (!$model) {
            return null;
        }

        $attributes = $this->cleanUpAttributes($attributes);

        return $this->update($model, $attributes);
    }

    /**
     * Delete a record.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool|null
     */
    public function deleteById($id): ?bool
    {
        $model = $this->findOrFail($id);
        return $this->delete($model);
    }

    /**
     * Search with filters and pagination.
     *
     * @param array $searchableFields
     * @param array $filters
     * @param int|null $perPage
     * @param array $relations
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection
     */
    public function search(array $searchableFields = [], array $filters = [], ?int $perPage = null, array $relations = [])
    {
        $query = $this->getQuery($relations);

        // Handle search
        if (!empty($filters['search']) && !empty($searchableFields)) {
            $searchTerm = $filters['search'];
            $query->where(function (Builder $q) use ($searchableFields, $searchTerm) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', '%' . addcslashes($searchTerm, '%_') . '%');
                }
            });
            unset($filters['search']); // Remove search from filters to avoid re-processing
        }

        // Handle other filters with support for operators
        $query = $this->applyFilters($query, $filters);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Clean up attributes to only include fillable fields.
     *
     * @param array $attributes
     * @return array
     */
    protected function cleanUpAttributes($attributes): array
    {
        return collect($attributes)->filter(function ($value, $key) {
            return $this->model->isFillable($key);
        })->toArray();
    }

    /**
     * Apply conditions to the query.
     *
     * @param Builder $query
     * @param array $conditions
     * @return Builder
     */
    protected function applyConditions(Builder $query, array $conditions): Builder
    {
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
        return $query;
    }

    /**
     * Apply filters with support for operators to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (str_contains($field, '.')) {
                // Handle relation filters
                $relations = explode('.', $field);
                $lastField = array_pop($relations);   // actual column
                $relationPath = implode('.', $relations); // relation path

                $query->whereHas($relationPath, function ($relQ) use ($lastField, $value) {
                    if (is_array($value) && count($value) >= 2 && in_array($value[0], ['>', '<', '>=', '<=', '!=', 'like'])) {
                        $operator = $value[0];
                        $actualValue = $value[1];
                        if ($operator === 'like') {
                            $actualValue = '%' . addcslashes($actualValue, '%_') . '%';
                        }
                        $relQ->where($lastField, $operator, $actualValue);
                    } elseif (is_array($value)) {
                        $relQ->whereIn($lastField, $value);
                    } else {
                        $relQ->where($lastField, $value);
                    }
                });
            } else {
                // Handle normal field filters
                if (is_array($value) && count($value) >= 2 && in_array($value[0], ['>', '<', '>=', '<=', '!=', 'like'])) {
                    $operator = $value[0];
                    $actualValue = $value[1];
                    if ($operator === 'like') {
                        $actualValue = '%' . addcslashes($actualValue, '%_') . '%';
                    }
                    $query->where($field, $operator, $actualValue);
                } elseif (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }
}
