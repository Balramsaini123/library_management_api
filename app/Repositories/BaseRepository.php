<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new record in the database.
     *
     * @param  array  $data  The data to be inserted.
     * @return \Illuminate\Database\Eloquent\Model The newly created model.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Retrieve a model by its UUID.
     *
     * @param  string  $uuid  The UUID of the model to be retrieved.
     * @return \Illuminate\Database\Eloquent\Model|null The model if found, null otherwise.
     */
    public function findByUuid(string $uuid)
    {
        return $this->model::where('uuid_column', $uuid)->first();
    }

    /**
     * Update a model by its UUID.
     *
     * @param  mixed  $user  The user data containing the UUID of the model to be updated.
     * @param  array  $data  The data to be updated.
     * @return \Illuminate\Database\Eloquent\Model|null The updated model if found, null otherwise.
     */
    public function update($user, array $data)
    {
        $model = $this->findByUuid($user->uuid_column);
        if ($model) {
            $model->update($data);

            return $model;
        }

        return null;
    }

    /**
     * Delete a model by its UUID.
     *
     * @param  mixed  $user  The user data containing the UUID of the model to be deleted.
     * @return bool True if the model is successfully deleted, false otherwise.
     */
    public function delete($user)
    {
        $model = $this->findByUuid($user->uuid_column);
        if ($model) {
            return $model->delete();
        }

        return false;
    }

    /**
     * Get all models in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of models.
     */
    public function getAll($perPage = 10)
    {
        return $this->model->orderBy('id', 'asc')->paginate($perPage);
    }
}
