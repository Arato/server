<?php

namespace Arato\Transformers;

class AlertTransformer extends Transformer
{
    public function transform($item)
    {
        return [
            'id'        => $item['id'],
            'title'     => $item['title'],
            'content'   => $item['content'],
            'price'     => $item['price'],
            'user_id'   => $item['user_id'],
            'createdAt' => $item['created_at']
        ];
    }
}