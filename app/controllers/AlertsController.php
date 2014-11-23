<?php

use Arato\Repositories\AlertRepository;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Arato\Transformers\AlertTransformer;

class AlertsController extends ApiController
{
    protected $alertTransformer;
    protected $alertRepository;

    function __construct(AlertTransformer $alertTransformer, AlertRepository $alertRepository)
    {
        //$this->beforeFilter('auth.basic', ['on' => 'post']);

        $this->alertTransformer = $alertTransformer;
        $this->alertRepository = $alertRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $alerts = $this->alertRepository->filter(Input::all());

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
        $isValidAlert = $this->alertRepository->isValid(Input::all());

        if (!$isValidAlert) {
            return $this->respondFailedValidation();
        }

        $createdUser = $this->alertRepository->create(Input::all());

        return $this->respondCreated($createdUser);
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
        $alert = Alert::find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        return $this->respond([
            'data' => $this->alertTransformer->transform($alert)
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

//        $isValidAlert = $this->alertRepository->isValid(Input::all());
//
//        if (!$isValidAlert) {
//            return $this->respondFailedValidation();
//        }

        $updatedUser = $this->alertRepository->update($id, Input::all());

        return $this->respond([
            'data' => $updatedUser
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
//        $alert = Alert::find($id);
//
//        if (!$alert) {
//            return $this->respondNotFound('Alert does not exist.');
//        }

        $this->alertRepository->delete($id);

        return $this->respondDeleted('Alert successfully deleted');
    }
}
