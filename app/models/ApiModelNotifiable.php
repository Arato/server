<?php


namespace models;


use BadMethodCallException;
use Eloquent;
use Illuminate\Support\Facades\Validator;
use models\enum\Action;
use Underscore\Types\Arrays;

class ApiModelNotifiable extends ApiModel
{

    /**
     * array for all notifiable properties
     * @var
     */
    protected $notifiable;

    public function getNotifiableProperties()
    {
        return $this->notifiable;
    }

    public function notifications()
    {
        return $this->morphMany('Notification', 'notifiable');
    }
}

