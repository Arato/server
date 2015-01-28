<?php


use models\ApiModel;

class Alert extends ApiModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'alerts';

    protected $fillable = ['title', 'price', 'content', 'user_id'];

    protected $commonRules = [
        'title' => 'required',
        'price' => ['required', 'integer', 'min:0']
    ];

    protected $rulesForCreation = [];
    protected $rulesForUpdate = [];

    public function user()
    {
        return $this->belongsTo('User');
    }
}
