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
        $this->beforeFilter('auth.basic', ['only' => ['update', 'delete']]);

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

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $inputs = Input::all();
        $validation = $this->userRepository->isValidForCreation('User', $inputs);

        if (!$validation->passes) {
            return $this->respondFailedValidation($validation->messages);
        }

        $inputs['password'] = Hash::make(Input::get('password'));

        $createdUser = $this->userRepository->create($inputs);

        return $this->respondCreated([
            'data' => $this->userTransformer->fullTransform($createdUser)
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
            'data' => $this->userTransformer->fullTransform($user)
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
        $inputs = Input::all();
        $inputs['id'] = $id;

        if (!$user) {
            return $this->respondNotFound('User does not exist.');
        }
        if (!$this->canConnectedUserEditElement($user['id'])) {
            return $this->respondForbidden();
        }

        $validation = $this->userRepository->isValidForUpdate('User', $inputs);

        if (!$validation->passes) {
            return $this->respondFailedValidation($validation->messages);
        }

        if (Input::get('password')) {
            $inputs['password'] = Hash::make(Input::get('password'));
        }

        $updatedUser = $this->userRepository->update($id, $inputs);

        return $this->respond([
            'data' => $this->userTransformer->fullTransform($updatedUser)
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

        if (!$this->canConnectedUserEditElement($user['id'])) {
            return $this->respondForbidden();
        }

        $this->userRepository->delete($id);

        return $this->respondNoContent();
    }
}
