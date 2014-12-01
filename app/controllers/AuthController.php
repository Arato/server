<?php


use controllers\ApiController;

class AuthController extends ApiController
{

    public function login()
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required|alphaNum'
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return $this->respondUnauthorized();
        }

        $userData = [
            'email'    => Input::get('email'),
            'password' => Input::get('password')
        ];

        if (Auth::attempt($userData, false)) {
            return $this->respond([
                'data' => Auth::user()
            ]);
        } else {
            return $this->respondUnauthorized();
        }
    }

    public function logout()
    {
        Auth::logout();

        return $this->respond([
            'message' => 'Successfully logged out.'
        ]);
    }
}