<?php

use Arato\Repositories\UserRepository;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Arato\Transformers\UserTransformer;

class UsersController extends ApiController
{
    protected $userTransformer;
    protected $userRepository;

    function __construct(UserRepository $userRepository, UserTransformer $userTransformer)
    {
        $this->beforeFilter('auth.basic', ['except' => 'store']);

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
            'users' => $this->userTransformer->transformCollection($users->all())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $validation = $this->userRepository->isValidForCreation(Input::all());

        if (!$validation->passes) {
            return $this->respondFailedValidation($validation->messages);
        }

        $data = Input::all();
        $data['password'] = Hash::make(Input::get('password'));

        $createdUser = $this->userRepository->create($data);

        return $this->respondCreated([
            'users' => $this->userTransformer->transform($createdUser)
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->respondNotFound('User does not exist.');
        }

        return $this->respond([
            'users' => $this->userTransformer->transform($user)
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->respondNotFound('User does not exist.');
        }
        if (Auth::user()->id !== $user['id']) {
            return $this->respondForbidden();
        }

        $validation = $this->userRepository->isValidForUpdate(Input::all(), $id);

        if (!$validation->passes) {
            return $this->respondFailedValidation($validation->messages);
        }

        $data = Input::all();
        if (Input::get('password')) {
            $data['password'] = Hash::make(Input::get('password'));
        }
        $updatedUser = $this->userRepository->update($id, $data);


        return $this->respond([
            'users' => $this->userTransformer->transform($updatedUser)
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->respondNotFound('User does not exist.');
        }

        if (Auth::user()->id !== $user['id']) {
            return $this->respondForbidden();
        }

        $this->userRepository->delete($id);

        return $this->respondNoContent();
    }
}
