<?php
namespace Arato\Repositories;

use Log;
use stdClass;
use Underscore\Parse;
use Illuminate\Support\Facades\Validator;
use Underscore\Types\Arrays;
use User;

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

        return $this->model->with([])->paginate($limit);
    }

    public function isValidForCreation(Array $data)
    {
        $rules = [
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed']
        ];

        $validator = Validator::make($data, $rules);
        $object = new stdClass();
        $object->passes = $validator->passes();
        $object->messages = $validator->messages()->toArray();

        return $object;
    }

    public function isValidForUpdate(Array $data)
    {
        $rules = [
            'email'    => ['email'],
            'password' => ['confirmed']
        ];

        $validator = Validator::make($data, $rules);
        $object = new stdClass();
        $object->passes = $validator->passes();
        $validator->messages = $validator->messages()->toArray();

        return $validator;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email);
    }
}