<?php namespace Fetch\v1\Models;

class Drawing extends \Eloquent {

	protected $fillable = ['userid', 'to_phone_hash', 'drawing', 'read'];

    public function createDrawing($data)
    {
        $drawing = new Drawing;
        $drawing->to_phone_hash = $data['to_phone_hash'];
        $drawing->userid = $data['userid'];
        $drawing->drawing = $data['drawing'];
        $drawing->read = 0;
        $drawing->save();
    }
}