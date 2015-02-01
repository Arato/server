<?php

use Arato\Repositories\AlertRepository;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Arato\Transformers\AlertTransformer;
use Underscore\Types\Arrays;

class AlertsController extends ApiController
{
    protected $alertTransformer;
    protected $alertRepository;
    protected $userRepository;

    function __construct(AlertTransformer $alertTransformer, AlertRepository $alertRepository, UserRepository $userRepository)
    {
        $this->beforeFilter('auth.basic', ['except' => ['index', 'show']]);

        $this->alertTransformer = $alertTransformer;
        $this->alertRepository = $alertRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $userId
     *
     * @return Response
     */
    public function index($userId = null)
    {
        if (!is_null($userId)) {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                return $this->respondNotFound('User does not exist.');
            }
        }

        $filters = Arrays::merge(Input::all(), ['userId' => $userId]);
        $alerts = $this->alertRepository->filter($filters);

        return $this->respondWithPagination($alerts, [
            'alerts' => $this->alertTransformer->transformCollection($alerts->all())
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

        $validation = $this->alertRepository->isValidForCreation('Alert', $inputs);

        if (!$validation->passes) {
            return $this->respondFailedValidation($validation->messages);
        }

        $inputs['user_id'] = Auth::user()->id;
        $createdAlert = $this->alertRepository->create($inputs);

        return $this->respondCreated([
            'alerts' => $this->alertTransformer->transform($createdAlert)
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
        $alert = $this->alertRepository->find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        return $this->respond([
            'alerts' => $this->alertTransformer->transform($alert)
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
        $alert = $this->alertRepository->find($id);
        $inputs = Input::all();

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        if (!$this->canConnectedUserEditElement($alert['user_id'])) {
            return $this->respondForbidden();
        }

        $validation = $this->alertRepository->isValidForUpdate('Alert', $inputs);

        if (!$validation->passes) {
            return $this->respondFailedValidation($validation->messages);
        }

        $inputs['user_id'] = Auth::user()->id;
        $updatedAlert = $this->alertRepository->update($id, $inputs);

        return $this->respond([
            'alerts' => $this->alertTransformer->transform($updatedAlert)
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
        $alert = $this->alertRepository->find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        if (!$this->canConnectedUserEditElement($alert['user_id'])) {
            return $this->respondForbidden();
        }

        $this->alertRepository->delete($id);

        return $this->respondNoContent();
    }
}
