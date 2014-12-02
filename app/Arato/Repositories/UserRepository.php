<?php
namespace Arato\Repositories;

use Underscore\Parse;
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
        $limit = Arrays::get($filters, 'limit');
        $limit = Parse::toInteger($limit);
        if ($limit > 50 || $limit <= 0) {
            $limit = $this->defaultLimit;
        }
        
        return $this->model->with([])->paginate($limit);
    }

    public function isValidForCreation(Array $data)
    {
        // TODO: Implement isValidForCreation() method.
    }

    public function isValidForUpdate(Array $data)
    {
        // TODO: Implement isValidForUpdate() method.
    }
}