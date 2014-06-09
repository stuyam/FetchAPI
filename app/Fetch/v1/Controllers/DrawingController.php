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
        $data = [
            'userid' =>Input::get('userid'),
            'to_phone_hash'   =>Input::get('to_phone_hash'),
            'drawing'     =>Input::get('drawing'),
        ];

        if( ! $this->validator->drawingCreateDrawing($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        Drawing::createDrawing($data);

    }

}