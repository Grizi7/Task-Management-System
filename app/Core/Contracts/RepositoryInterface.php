<?php

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface RepositoryInterface
{
    public function getQuery(array $relations = []): Builder;
    public function all(array $relations = [], array $columns = ['*'], array $filters = [], array $withCount = []): Collection;
    public function paginate(int $perPage = 15, array $relations = [], array $columns = ['*'], array $filters = [], array $withCount = []): LengthAwarePaginator;
    public function find($id, array $relations = [], array $columns = ['*']): ?Model;
    public function findOrFail($id, array $relations = [], array $columns = ['*']): Model;
    public function findOneBy(array $conditions, array $relations = []): ?Model;
    public function findBy(array $conditions, array $relations = []): Collection;
    public function create(array $attributes): Model;
    public function update(Model $model, array $attributes): Model;
    public function updateById($id, array $attributes): Model;
    public function updateBy($conditions, $value = null, array $attributes = []): ?Model;
    public function delete(Model $model): bool;
    public function deleteById($id): ?bool;
    public function search(array $searchableFields = [], array $filters = [], ?int $perPage = null, array $relations = []);
}