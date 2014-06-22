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
	public function index()
	{
        $data = [
            'phone_hash' => Input::get('phone_hash'),
        ];

        if( ! $this->validator->inboxIndex($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }
        $raw = $this->drawing->getInbox($data);

        $newData = [];
        $inboxMap = [];

        foreach($raw as $n)
        {
            if (in_array($n['username'], $inboxMap))
            {
                $newData[array_search($n['username'], $inboxMap)]['drawings'][] = [
                    'drawing_id' => $n['id'],
                    'width'      => $n['width'],
                    'height'     => $n['height'],
                    'line_color' => $n['line_color'],
                    'bg_color'   => $n['bg_color'],
                    'pages'      => json_decode($n['drawing']),
                    'version'    => $n['version'],
                    'timestamp'  => $n['timestamp'],
                ];
            }
            else
            {
                $newData[] = [
                    'username'   => $n['username'],
                    'name'       => $n['name'],
                    'phone_hash' => $n['phone_hash'],
                    'drawings'  => [
                        [
                            'drawing_id' => $n['id'],
                            'width'      => $n['width'],
                            'height'     => $n['height'],
                            'line_color' => $n['line_color'],
                            'bg_color'   => $n['bg_color'],
                            'pages'      => json_decode($n['drawing']),
                            'version'    => $n['version'],
                            'timestamp'  => $n['timestamp'],
                        ]
                    ],
                ];
                $inboxMap[] = $n['username'];
            }
        }

        return $this->respond($newData);
	}

}