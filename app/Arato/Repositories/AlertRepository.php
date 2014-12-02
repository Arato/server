<?php
namespace Arato\Repositories;

use Alert;
use Illuminate\Support\Facades\Validator;
use Underscore\Parse;
use Underscore\Types\Arrays;
use Pirminis\Maybe;

class AlertRepository extends Repository
{
    public function __construct(Alert $model)
    {
        parent::__construct($model);
    }

    public function filter(Array $filters)
    {
        $query = $this->model;

        $userId = Maybe(Arrays::get($filters, 'userId'))
            ->val();

        if ($userId) {
            $query = $query->where('user_id', '=', $userId);
        }

        $limit = Maybe(Arrays::get($filters, 'limit'))
            ->map(function ($maybe) {
                $limit = Parse::toInteger($maybe->val($this->defaultLimit));

                return $limit <= 50 && $limit > 0 ? $limit : $this->defaultLimit;
            })
            ->val($this->defaultLimit);

        $availableSorts = ['created_at', 'price'];

        $sortBy = Maybe(Arrays::get($filters, 'sort'))
            ->map(function ($maybe) use ($availableSorts) {
                $sort = $maybe->val();

                return Arrays::contains($availableSorts, $maybe->val())
                    ? $sort
                    : $this->defaultSort;
            })
            ->val($this->defaultSort);

        $availableOrders = ['asc', 'desc'];
        $order = Maybe(Arrays::get($filters, 'order'))
            ->map(function ($maybe) use ($availableOrders) {
                $order = $maybe->val();

                return Arrays::contains($availableOrders, $maybe->val())
                    ? $order
                    : $this->defaultOrder;
            })
            ->val($this->defaultOrder);

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