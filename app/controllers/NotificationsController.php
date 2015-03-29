<?php

use Arato\Repositories\AlertRepository;
use Arato\Repositories\NotificationRepository;
use Arato\Transformers\NotificationTransformer;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Underscore\Types\Arrays;

class NotificationsController extends ApiController
{
    protected $notificationTransformer;
    protected $notificationRepository;
    protected $alertRepository;

    function __construct(NotificationTransformer $notificationTransformer, NotificationRepository $notificationRepository, AlertRepository $alertRepository)
    {
        $this->beforeFilter('auth.basic');

        $this->notificationTransformer = $notificationTransformer;
        $this->notificationRepository = $notificationRepository;
        $this->alertRepository = $alertRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $alertId
     *
     * @return Response
     */
    public
    function index($alertId = null)
    {
        if (!is_null($alertId)) {
            $alert = $this->alertRepository->find($alertId, true);

            if (!$alert) {
                return $this->respondNotFound('Alert does not exist.');
            }
        }

        $filters = Arrays::merge(Input::all(), ['alertId' => $alertId]);
        $notifications = $this->notificationRepository->filter($filters);

        return $this->respondWithPagination($notifications, [
            'data' => $this->notificationTransformer->transformCollection($notifications->all())
        ]);
    }
}
