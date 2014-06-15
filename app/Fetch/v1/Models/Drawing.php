<?php namespace Fetch\v1\Models;

use Fetch\v1\Models\User;

class Drawing extends \Eloquent {

	protected $fillable = ['userid', 'to_phone_hash', 'drawing', 'read'];

    public function createDrawingReturnMissingHashes($data)
    {
        $missing = FALSE;
        $hashArray = json_decode($data['to_phone_hash']);
        foreach($hashArray as $hash)
        {
            $drawing = new Drawing;
            $drawing->to_phone_hash = $hash;
            $drawing->userid = $data['userid'];
            $drawing->drawing = $data['drawing'];
            $drawing->read = 0;
            $drawing->save();

            if( ! User::where('phone_hash', '=', $hash)->first())
            {
                $missing[] = $hash;
            }
        }
        return $missing;
    }

}