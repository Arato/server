<?php

namespace Arato\Service;

class AlertService extends Service
{

    public function filter(Array $filters)
    {
        $limit = ($filters['limit'] && $filters['limit'] < 50)
            ? $filters['limit']
            : 20;

        $contains = ['created_at', 'price'];
        $sort = ($filters['sort'] && Arrays::contains($contains, $filters['sort']))
            ? $filters['sort']
            : 'created_at';

        $order = $filters['order'] && $filters['order'] == 'desc';

        return Alert::where()
            ->sortBy($sort, null, $order)
            ->paginate($limit);
    }

    public function create($item)
    {
        // TODO: Implement create() method.
    }
}