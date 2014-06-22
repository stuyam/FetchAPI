<?php namespace Fetch\v1\Models;

use Fetch\v1\Models\User;

class Linkable extends \Eloquent {

	protected $fillable = ['user_id', 'drawing', 'width', 'height', 'bg_color', 'line_color', 'version', 'timestamp'];

    public $timestamps = false;

    protected $table = 'linkable_drawings';

    public function createLinkableDrawing($data)
    {
        $drawing = new Linkable;
        $drawing->user_id       = $data['userid'];
        $drawing->drawing       = json_encode($data['pages']);
        $drawing->width         = $data['width'];
        $drawing->height        = $data['height'];
        $drawing->bg_color      = $data['bg_color'];
        $drawing->line_color    = $data['line_color'];
        $drawing->version       = $data['version'];
        $drawing->timestamp     = time();
        $drawing->save();
        return $drawing->id;
    }

}