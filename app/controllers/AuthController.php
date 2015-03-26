<?php


use Arato\Transformers\UserTransformer;
use controllers\ApiController;

class AuthController extends ApiController
{
    protected $userTransformer;

    function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }

    public function login()
    {
        $rules = [
            'email'    => ['required', 'email'],
            'password' => ['required', 'alphaNum']
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return $this->respondUnauthorized();
        }

        $userData = [
            'email'    => Input::get('email'),
            'password' => Input::get('password')
        ];

        if (Auth::attempt($userData)) {
            return $this->respond([
                'data' => $this->userTransformer->fullTransform(Auth::user())
            ]);
        } else {
            return $this->respondUnauthorized();
        }
    }

    public function logout()
    {
        Auth::logout();

        return $this->respondNoContent();
    }
}
