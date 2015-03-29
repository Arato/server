<?php
namespace Arato\Repositories;

use Notification;
use Underscore\Parse;
use Underscore\Types\Arrays;

class NotificationRepository extends Repository
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function filter(Array $filters)
    {
        $query = $this->model;

        $limit = Maybe(Arrays::get($filters, 'limit'))
            ->map(function ($maybe) {
                $limit = Parse::toInteger($maybe->val($this->defaultLimit));

                return $limit <= 50 && $limit > 0 ? $limit : $this->defaultLimit;
            })
            ->val($this->defaultLimit);

        $alertId = Maybe(Arrays::get($filters, 'alertId'))
            ->val();

        if ($alertId) {
            $query = $query->where('notifiable_id', '=', $alertId)
                ->where('notifiable_type', '=', 'Alert');
        }

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }
}
