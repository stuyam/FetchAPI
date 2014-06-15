<?php namespace Fetch\v1\Controllers;

use Fetch\v1\Services\Validator;
use Fetch\v1\Models\Drawing;
use Fetch\v1\Models\User;
use Fetch\v1\Models\Linkable;
use \Input, \App, \Sms;

class DrawingController extends APIController {

    protected $validator;
    protected $drawing;
    protected $linkable;

	public function __construct(Validator $validator, Drawing $drawing, Linkable $linkable)
    {
        $this->validator = $validator;
        $this->drawing = $drawing;
        $this->linkable = $linkable;
    }

    public function postCreate()
    {
        $data = [
            'userid'        => Input::get('userid'),
            'to_phone_hash' => Input::get('to_phone_hash'),
            'drawing'       => Input::get('drawing'),
        ];

        if( ! $this->validator->drawingCreateDrawing($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        return $this->respond($this->drawing->createDrawingReturnMissingHashes($data));
    }

    public function postCreateLinkable()
    {
        $data = [
            'userid'        => Input::get('userid'),
            'drawing'       => Input::get('drawing'),
        ];

        if( ! $this->validator->drawingCreateLinkable($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $this->linkable->createLinkableDrawing($data);

        return $this->respondWithNoContent();
    }

    public function postMissingPhones()
    {
        $data = [
            'userid'         => Input::get('userid'),
            'missing_phones' => Input::get('missing_phones'),
        ];

        if( ! $this->validator->drawingMissingPhones($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $name = User::findOrFail($data['userid']);
        $name = ['name' => $name->name];

        if( ! App::environment('testing'))
        {
            array_map(function($n, $data)
            {
                Sms::send([
                    'to'   => $n['phone'],
                    'text' =>
                        "Hi $n[name], $data[name] sent you a drawing on Fetch! Download it here to view what they sent you: http://bit.ly/GetFetch"
                ]);
            }, json_decode($data['missing_phones'], true), $name);
        }

        return $this->respondWithNoContent();
    }

}