<?php namespace Fetch\v1\Controllers;

use Fetch\v1\Services\Validator;
use Fetch\v1\Models\Drawing;
use Illuminate\Support\Facades\Input;

class DrawingController extends APIController {

    protected $validator;
    protected $drawing;

	public function __construct(Validator $validator, Drawing $drawing)
    {
        $this->validator = $validator;
        $this->drawing = $drawing;
    }

    public function postCreate()
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

        $this->drawing->createDrawing($data);

        return 'testsd';
    }

}