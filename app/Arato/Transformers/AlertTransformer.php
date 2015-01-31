<?php

namespace Arato\Transformers;

class AlertTransformer extends Transformer
{
    public function transform($item)
    {
        return [
            'id'         => $item['id'],
            'title'      => $item['title'],
            'price'      => $item['price'],
            'content'    => $item['content'],
            'user'       => [
                'id'    => $item->user['id'],
                'email' => $item->user['email']
            ],
            'created_at' => $item['created_at']->toIso8601String(),
            'updated_at' => $item['updated_at']->toIso8601String()
        ];
    }
}