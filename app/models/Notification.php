<?php


use models\ApiModel;

class Notification extends ApiModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    protected $fillable = ['type', 'notifiable_id', 'notifiable_type'];

    protected $commonRules = [];

    protected $rulesForCreation = [];
    protected $rulesForUpdate = [];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function entries()
    {
        return $this->hasMany('NotificationEntry');
    }
}
