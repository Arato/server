<?php

abstract class Service
{
    protected $defaultLimit = 20;
    protected $defaultSort = 'id';
    protected $defaultOrder = 'desc';

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public abstract function filter(Array $filters);


    public function all()
    {
        return $this->model->all()->toArray();
    }

    public function allWith(array $with)
    {
        return $this->model->with($with)->get()->toArray();
    }

    public function create($input)
    {
        return $this->model->create($input)->toArray();
    }

    public function update($id, $input)
    {
        return $this->model->find($id)->update($input);
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            return $model->toArray();
        }

        return null;
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}