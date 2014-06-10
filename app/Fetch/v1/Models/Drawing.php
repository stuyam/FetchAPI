<?php namespace Fetch\v1\Models;

use Fetch\v1\Models\User;

class Drawing extends \Eloquent {

	protected $fillable = ['userid', 'to_phone_hash', 'drawing', 'read'];

    public function createDrawing($data)
    {
        $missing = FALSE;
        for($i = 0; $i < count($data['to_phone_hash']); $i++)
        {
            $drawing = new Drawing;
            $drawing->to_phone_hash = $data['to_phone_hash'][$i];
            $drawing->userid = $data['userid'];
            $drawing->drawing = $data['drawing'];
            $drawing->read = 0;
            $drawing->save();

            if( ! User::where('phone_hash', '=', $data['to_phone_hash'][$i])->first())
            {
                $missing[] = $data['to_phone_hash'][$i];
            }
        }
        return $missing;
    }
}