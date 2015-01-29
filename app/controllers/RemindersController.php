<?php
use Arato\Repositories\UserRepository;
use controllers\ApiController;
use Illuminate\Auth\Reminders\ReminderRepositoryInterface;

class RemindersController extends ApiController
{
    protected $userRepository;
    protected $reminders;

    function __construct(UserRepository $userRepository, ReminderRepositoryInterface $reminders)
    {
        $this->userRepository = $userRepository;
        $this->reminders = $reminders;
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
            case
            Password::INVALID_USER:
                return $this->setStatusCode(400)->respondWithError(Lang::get($response));
            case Password::REMINDER_SENT:
                return $this->respondNoContent();
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
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return $this->setStatusCode(400)->respondWithError(Lang::get($response));

            case Password::PASSWORD_RESET:
                return $this->respondNoContent();
        }
    }
}
