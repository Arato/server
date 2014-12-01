<?php


use controllers\ApiController;

class LoginController extends ApiController
{

    public function login()
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required|alphaNum'
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return $this->respondFailedValidation();
        }

        $userData = [
            'email'    => Input::get('email'),
            'password' => Input::get('password')
        ];

        if (Auth::attempt($userData)) {
            return $this->respond([
                'data' => $userData
            ]);
        } else {
            return $this->respondFailedValidation();
        }
    }

    public function logout()
    {
        Auth::logout;

        return $this->respond([
            'message' => 'Successfully logged out.'
        ]);
    }
}