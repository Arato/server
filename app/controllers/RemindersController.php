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

        $user = $this->userRepository->findByEmail('email', Input::only('email'));

        if (is_null($user)) {
            return $this->setStatusCode(400)->respondWithError('No valid user for this email');
        }

        $token = $this->reminders->create($user);

        return $this->respond("");
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     *
     * @return Response
     */
    public function getReset($token = null)
    {
        if (is_null($token)) App::abort(404);

        return View::make('password.reset')->with('token', $token);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset()
    {
        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);

            $user->save();
        });

        switch ($response) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));

            case Password::PASSWORD_RESET:
                return Redirect::to('/');
        }
    }

}
