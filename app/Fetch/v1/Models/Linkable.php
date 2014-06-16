<?php namespace Fetch\v1\Models;

use Fetch\v1\Models\User;

class Linkable extends \Eloquent {

	protected $fillable = ['userid', 'drawing'];

    protected $table = 'linkable_drawings';

    public function createLinkableDrawing($data)
    {
        $drawing = new Linkable;
        $drawing->userid = $data['userid'];
        $drawing->drawing = $data['drawing'];
        $drawing->save();
        return $drawing->id;
    }

}