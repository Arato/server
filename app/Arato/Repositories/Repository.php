<?php

namespace Arato\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class Repository
{
    protected $defaultLimit = 20;
    protected $defaultSort = 'id';
    protected $defaultOrder = 'desc';

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public abstract function filter(Array $filters);

    public abstract function isValidForCreation(Array $data);

    public abstract function isValidForUpdate(Array $data);


    public function all()
    {
        return $this->model->all()->toArray();
    }

    public function allWith(array $with)
    {
        return $this->model->with($with)->get()->toArray();
    }

    public function create($inputs)
    {
        return $this->model->create($inputs);
    }

    public function update($id, $input)
    {
        $updated = $this->model->find($id)->update($input);

        if ($updated) {
            return $this->model->find($id);
        }

        return null;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            return $model;
        }

        return null;
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}