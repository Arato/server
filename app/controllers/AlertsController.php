<?php

use Arato\Push\PushService;
use Arato\Repositories\AlertRepository;
use Arato\Repositories\UserRepository;
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
            'data' => $this->alertTransformer->transformCollection($alerts->all())
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

        $response = [
            'data' => $this->alertTransformer->fullTransform($createdAlert)
        ];

        return $this->respondCreated($response);
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
            'data' => $this->alertTransformer->fullTransform($alert)
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
        $inputs['id'] = $id;

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

        $response = [
            'data' => $this->alertTransformer->fullTransform($updatedAlert)
        ];

        return $this->respond($response);
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
