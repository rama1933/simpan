<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find record by ID
     */
    public function find($id, array $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find record by ID or throw exception
     */
    public function findOrFail($id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Create new record
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record by ID
     */
    public function update($id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    /**
     * Delete record by ID
     */
    public function delete($id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    /**
     * Get paginated records
     */
    public function paginate($perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Find records by conditions
     */
    public function where(array $conditions, array $columns = ['*']): Collection
    {
        return $this->model->where($conditions)->get($columns);
    }

    /**
     * Find first record by conditions
     */
    public function firstWhere(array $conditions, array $columns = ['*'])
    {
        return $this->model->where($conditions)->first($columns);
    }
}

