<?php namespace Fetch\v1\Controllers;

use Fetch\v1\Services\Validator;
use Illuminate\Support\Facades\Input;

class DrawingController extends APIController {

    protected $validator;

	public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function postCreateDrawing()
    {
        $from_userid = Input::get('from_userid');
        $to_userid = Input::get('to_userid');
        $drawing = Input::get('drawing');

        if( ! $this->validator->drawingCreateDrawing(Input::all()) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }
    }

}