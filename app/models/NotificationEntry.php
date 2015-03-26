<?php


use models\ApiModel;

class NotificationEntry extends ApiModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notificationentries';

    protected $fillable = ['field', 'previousValue', 'newValue'];

    public $timestamps = false;

    protected $commonRules = [];

    protected $rulesForCreation = [];
    protected $rulesForUpdate = [];
}
