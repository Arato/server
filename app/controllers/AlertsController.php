<?php

use Arato\Service\AlertService;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Arato\Transformers\AlertTransformer;

class AlertsController extends ApiController
{
    protected $alertTransformer;
    protected $alertService;

    function __construct(AlertTransformer $alertTransformer, AlertService $alertService)
    {
        $this->alertTransformer = $alertTransformer;
        $this->alertService = $alertService;

        $this->beforeFilter('auth.basic', ['on' => 'post']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $alerts = $this->alertService->filter(Input::all());

        return $this->respondWithPagination($alerts, [
            'data' => $this->alertTransformer->transformCollection($alerts->all())
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if (!Input::get('title') || !Input::get('price')) {
            return $this->respondFailedValidation();
        }

        Alert::create(Input::all());

        return $this->respondCreated('Alert successfully created');
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
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
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
        //
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
        $alert = Alert::find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        $alert->remove();

        return $this->respondDeleted('Alert successfully deleted');
    }
}
