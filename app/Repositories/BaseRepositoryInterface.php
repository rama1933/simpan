<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*']);

    /**
     * Find record by ID
     */
    public function find($id, array $columns = ['*']);

    /**
     * Find record by ID or throw exception
     */
    public function findOrFail($id, array $columns = ['*']);

    /**
     * Create new record
     */
    public function create(array $data);

    /**
     * Update record by ID
     */
    public function update($id, array $data);

    /**
     * Delete record by ID
     */
    public function delete($id);

    /**
     * Get paginated records
     */
    public function paginate($perPage = 15, array $columns = ['*']);

    /**
     * Find records by conditions
     */
    public function where(array $conditions, array $columns = ['*']);

    /**
     * Find first record by conditions
     */
    public function firstWhere(array $conditions, array $columns = ['*']);
}

