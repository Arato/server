<?php
namespace Arato\Repositories;

use Alert;
use Illuminate\Support\Facades\Validator;
use Underscore\Parse;
use Underscore\Types\Arrays;

class AlertRepository extends Repository
{
    public function __construct(Alert $model)
    {
        parent::__construct($model);
    }

    public function filter(Array $filters)
    {
        $query = $this->model;

        $userId = Arrays::get($filters, 'userId');

        if ($userId) {
            $query = $query->where('user_id', '=', $userId);
        }

        $limit = Arrays::get($filters, 'limit');
        $limit = Parse::toInteger($limit);
        if ($limit > 50 || $limit <= 0) {
            $limit = $this->defaultLimit;
        }

        $availableSorts = ['created_at', 'price'];

        $sortBy = Arrays::get($filters, 'sort');
        if ($sortBy) {
            $sortBy = Arrays::contains($availableSorts, $sortBy) ? $sortBy : $this->defaultSort;
        } else {
            $sortBy = $this->defaultSort;
        }

        $availableOrders = ['asc', 'desc'];
        $order = Arrays::get($filters, 'order');
        if ($order) {
            $order = Arrays::contains($availableOrders, $order) ? $order : $this->defaultOrder;
        } else {
            $order = $this->defaultOrder;
        }

        return $query->with([])->orderBy($sortBy, $order)->paginate($limit);
    }

    public function isValidForCreation(Array $data)
    {
        $rules = [
            'title' => 'required',
            'price' => ['required', 'min:0']
        ];

        $validator = Validator::make($data, $rules);

        return $validator->passes();
    }

    public function isValidForUpdate(Array $data)
    {
        $rules = [
            'price' => ['min:0']
        ];

        $validator = Validator::make($data, $rules);

        return $validator->passes();
    }
}