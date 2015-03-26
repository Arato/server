<?php


namespace Arato\Transformers;


class NotificationEntryTransformer extends Transformer
{

    public function basicTransform($item)
    {
        return [
            'id'            => $item['id'],
            'field'         => $item['field'],
            'previousValue' => $item['previousValue'],
            'newValue'      => $item['newValue']
        ];
    }

    public function extendedTransform($item)
    {
        return $this->basicTransform($item);
    }

    public function fullTransform($item)
    {
        return $this->extendedTransform($item);
    }
}