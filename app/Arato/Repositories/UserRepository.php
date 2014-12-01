<?php
namespace Arato\Repositories;

use Underscore\Parse;
use Underscore\Types\Arrays;

class UserRepository extends Repository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function filter(Array $filters)
    {
        $limit = Maybe(Arrays::get($filters, 'limit'))
            ->map(function ($maybe) {
                $limit = Parse::toInteger($maybe->val($this->defaultLimit));

                return $limit <= 50 ? $limit : $this->defaultLimit;
            })
            ->val($this->defaultLimit);

        return $this->model->with([])->orderBy($sortBy, $order)->paginate($limit)
    }
}