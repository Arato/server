<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use models\ApiModel;

class User extends ApiModel implements UserInterface, RemindableInterface
{

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $fillable = ['email', 'password', 'sequence_number'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];


    protected $commonRules = [
        'email' => ['required', 'email', 'unique:users'],
    ];

    protected $rulesForCreation = [
        'password' => ['required', 'confirmed']
    ];

    protected $rulesForUpdate = [
        'password' => ['confirmed']
    ];
}
