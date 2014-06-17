<?php namespace Fetch\v1\Controllers;

use Fetch\v1\Services\Validator;
use Fetch\v1\Models\Drawing;
use \Input;

class InboxController extends APIController {

    protected $validator;
    protected $drawing;

    public function __construct(Validator $validator, Drawing $drawing)
    {
        $this->validator = $validator;
        $this->drawing = $drawing;
    }

	/**
	 * @return Response
	 */
	public function post()
	{
        $data = [
            'userid' => Input::get('userid'),
        ];

        if( ! $this->validator->inboxIndex($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        return $this->respond($this->drawing->getInbox($data));
	}

}