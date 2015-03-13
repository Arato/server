<?php
use controllers\ApiController;


class RemindersController extends ApiController
{
    function __construct()
    {
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return Response
     */
    public function remind()
    {
        $response = Password::remind(Input::only('email'), function ($message) {
            $message->subject('Password Reminder');
        });
        switch ($response) {
            case Password::REMINDER_SENT:
                return $this->respondNoContent();
            case Password::INVALID_USER:
                return $this->respondNotFound('User does not exist.');
            default :
                return $this->setStatusCode(404)->respondWithError(Lang::get($response));
        }
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function reset()
    {
        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );


        Password::validator(function ($credentials) {
            return strlen($credentials['password']) >= 1;
        });

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);

            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->respondNoContent();
            case Password::INVALID_USER:
                return $this->respondNotFound('User does not exist.');
            case Password::INVALID_PASSWORD:
                return $this->setStatusCode(400)->respondWithError("Invalid password");
            case Password::INVALID_TOKEN:
                return $this->setStatusCode(400)->respondWithError("Invalid token");
            default:
                return $this->setStatusCode(400)->respondWithError(Lang::get($response));
        }
    }
}
