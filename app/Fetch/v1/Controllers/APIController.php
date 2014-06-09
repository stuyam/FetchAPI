<?php namespace Fetch\v1\Controllers;

use \Response;

class APIController extends \BaseController {

    protected $statusCode = 200;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    //------------------------ 200 ----------------------//

    public function respondWithNoContent($message = NULL)
    {
        return $this->setStatusCode(204)->respondWithError($message);
    }

    //-------------------- 200 Objects ------------------//

    public function respondWithLoginObject($object)
    {
        return $this->respond([
            'userid' => $object->id,
            'token' => $object->token,
            'name' => $object->name,
            'username' => $object->username,
            'country_code' => $object->country_code,
            'phone' => $object->phone,
        ]);
    }

    //------------------------ 400 ----------------------//

    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    public function respondMissingParameters($message = 'Missing Post Parameters')
    {
        return $this->setStatusCode(460)->respondWithError($message);
    }

    public function respondWith400($message = '400 Error')
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    //------------------- General Responses ---------------//

    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' =>$this->getStatusCode()
            ]
        ]);
    }


} 