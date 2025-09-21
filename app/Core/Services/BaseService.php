<?php

namespace App\Core\Services;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Core\Contracts\ServiceInterface;

class BaseService implements ServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
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
        return $this->repository->all($relations, $columns, $filters, $withCount);
    }

    /**
     * Paginate records.
     *
     * @param int $perPage
     * @param array $relations
     * @param array $columns
     * @param array $filters
     * @param array $withCount
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $relations = [], array $columns = ['*'], array $filters = [], array $withCount = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $relations, $columns, $filters, $withCount);
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
        return $this->repository->find($id, $relations, $columns);
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
        return $this->repository->findOrFail($id, $relations, $columns);
    }

    /**
     * Find one by field.
     *
     * @param string $field
     * @param mixed $value
     * @param array $relations
     * @return Model|null
     */
    public function findOneBy(array $conditions, array $relations = []): ?Model
    {
        return $this->repository->findOneBy($conditions, $relations);
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
        return $this->repository->findBy($conditions, $relations);
    }

    /**
     * Create a new record.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    /**
     * Update a model.
     *
     * @param Model $model
     * @param array $attributes
     * @return Model
     */
    public function update(Model $model, array $attributes): Model
    {
        return $this->repository->update($model, $attributes);
    }

    /**
     * Update a record by ID.
     *
     * @param int|string $id
     * @param array $attributes
     * @return Model|null
     */
    public function updateById($id, array $attributes): ?Model
    {
        return $this->repository->updateById($id, $attributes);
    }

    /**
     * Delete a model.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $this->repository->delete($model);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool|null
     */
    public function deleteById($id): ?bool
    {
        return $this->repository->deleteById($id);
    }

    /**
     * Search with filters and pagination.
     *
     * @param array $searchableFields
     * @param array $filters
     * @param int|null $perPage
     * @param array $relations
     * @return LengthAwarePaginator|Collection
     */
    public function search(array $searchableFields = [], array $filters = [], ?int $perPage = null, array $relations = [])
    {
        return $this->repository->search($searchableFields, $filters, $perPage, $relations);
    }

}
