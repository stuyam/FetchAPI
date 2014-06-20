<?php namespace Fetch\v1\Controllers;

use Fetch\v1\Services\Validator;
use Fetch\v1\Models\Drawing;
use Fetch\v1\Models\User;
use Fetch\v1\Models\Linkable;
use \Input, \App, \Sms, \Base62;

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
            'pages'       => Input::get('pages'),
            'width'         => Input::get('width'),
            'height'        => Input::get('height'),
            'line_color'    => Input::get('line_color'),
            'bg_color'      => Input::get('bg_color'),
            'version'       => Input::get('version'),
        ];

        if( ! $this->validator->drawingCreateDrawing($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $response = $this->drawing->createDrawingReturnMissingHashes($data);

        if($response)
        {
            return $this->respondNeedsMoreData($response);
        }
        return $this->respondWithNoContent();
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

        $id = $this->linkable->createLinkableDrawing($data);
        $string = Base62::encode($id);
        return $this->respond($string);
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

        $name = User::find($data['userid']);
        $fromName = $name->name;

        if( ! App::environment('testing'))
        {
            array_map(function($n) use ($fromName)
            {
                $toName = strtok($n['name'], " ");
                Sms::send([
                    'to'   => $n['phone'],
                    'text' =>
                        "Hi $toName, $fromName sent you a drawing on Fetch! Download it here to view what they sent you: http://bit.ly/GetFetch"
                ]);
            }, $data['missing_phones']);
        }

        return $this->respondWithNoContent();
    }

}