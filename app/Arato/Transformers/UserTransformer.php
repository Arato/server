<?php


namespace Arato\Transformers;


class UserTransformer extends Transformer
{
    public function transform($item)
    {
        return [
            'id'    => $item['id'],
            'email' => $item['email']
        ];
    }

}