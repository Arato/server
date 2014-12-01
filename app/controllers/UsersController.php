<?php

use Arato\Repositories\UserRepository;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Arato\Transformers\UserTransformer;

class UsersControllers extends ApiController
{
    protected $userTransformer;
    protected $userRepository;

    function __construct(UserRepository $userRepository, UserTransformer $userTransformer)
    {
        $this->beforeFilter('auth.api', ['on' => 'post', 'put']);

        $this->userRepository = $userRepository;
        $this->userTransformer = $userTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = $this->userRepository->filter(Input::all());

        return $this->respondWithPagination($users, [
            'data' => $this->userTransformer->transformCollection($users->all())
        ]);
    }
}