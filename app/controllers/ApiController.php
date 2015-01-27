<?php


namespace controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response as IlluminateResponse;

class ApiController extends \BaseController
{

    protected $statusCode = IlluminateResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }


    public function canConnectedUserEditElement($id)
    {
        return Auth::user()->id == $id;
    }

    /**
     * @param       $data    - data to send trough the API
     * @param array $headers - optional headers for the HTTP Response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data = [], $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }

    public function respondWithPagination(Paginator $items, $data)
    {
        $response = array_merge($data, [
            'paginate' => [
                'total_count'  => $items->getTotal(),
                'total_pages'  => ceil($items->getTotal() / $items->getPerPage()),
                'current_page' => $items->getCurrentPage(),
                'limit'        => $items->getPerPage()
            ]
        ]);

        return $this->respond($response);
    }


    /**
     * @param Array $data
     *
     * @return mixed
     */
    public function respondCreated($data)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond($data);
    }

    /**
     *
     * @return mixed
     */
    public function respondNoContent()
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_NO_CONTENT)
            ->respond();
    }


    public function respondNotFound($message = 'Not Found !')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondInternalError($message = 'Internal Error !')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function respondFailedValidation($message = 'Parameters failed validation')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function respondUnauthorized($message = 'Invalid credentials')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function respondForbidden($message = 'Forbidden')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)
            ->respondWithError($message);
    }
}
