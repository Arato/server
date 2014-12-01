<?php


class Alert extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'alerts';

    protected $fillable = ['title', 'price', 'content'];
}