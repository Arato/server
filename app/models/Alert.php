<?php


use models\ApiModelNotifiable;

class Alert extends ApiModelNotifiable
{
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'alerts';

    protected $fillable = ['title', 'price', 'content', 'user_id'];

    protected $notifiable = ['title', 'price', 'content'];

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
