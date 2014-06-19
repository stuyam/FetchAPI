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
	public function postGet()
	{
        $data = [
            'userid' => Input::get('userid'),
        ];

        if( ! $this->validator->inboxIndex($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }
        $raw = $this->drawing->getInbox($data);

        foreach($raw as $n)
        {
            if (in_array($n['username'], $inboxMap))
            {
                $newData[array_search($n['username'], $inboxMap)]['pages'][] = [
                    'drawing_id' => $n['id'],
                    'width'      => $n['width'],
                    'height'     => $n['height'],
                    'line_color' => $n['line_color'],
                    'bg_color'   => $n['bg_color'],
                    'pages'      => $n['drawing'],
                    'version'    => $n['version'],
                    'timestamp'  => $n['timestamp'],
                ];
            }
            else
            {
                $newData[] = [
                    'username' => $n['username'],
                    'name'     => $n['name'],
                    'pages'  => [[
                        'drawing_id' => $n['id'],
                        'width'      => $n['width'],
                        'height'     => $n['height'],
                        'line_color' => $n['line_color'],
                        'bg_color'   => $n['bg_color'],
                        'pages'      => $n['drawing'],
                        'version'    => $n['version'],
                        'timestamp'  => $n['timestamp'],
                    ]],
                ];
                $inboxMap[] = $n['username'];
            }
        }

        return $this->respond($newData);
	}

}