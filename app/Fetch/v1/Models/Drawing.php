<?php namespace Fetch\v1\Models;

use Fetch\v1\Models\User;

class Drawing extends \Eloquent {

	protected $fillable = ['user_id', 'to_phone_hash', 'drawing', 'width', 'height', 'line_color', 'bg_color', 'version', 'read'];

    protected function getDateFormat()
    {
        return 'U';
    }

    public function createDrawingReturnMissingHashes($data)
    {
        $missing = FALSE;
        $hashArray = $data['to_phone_hash'];
        foreach($hashArray as $hash)
        {
            $drawing = new Drawing;
            $drawing->user_id       = $data['userid'];
            $drawing->to_phone_hash = $hash;
            $drawing->drawing       = json_encode($data['drawing']);
            $drawing->width         = $data['width'];
            $drawing->height        = $data['height'];
            $drawing->bg_color      = $data['bg_color'];
            $drawing->line_color    = $data['line_color'];
            $drawing->version       = $data['version'];
            $drawing->read          = 0;
            $drawing->save();

            if( ! User::where('phone_hash', '=', $hash)->first())
            {
                $missing[] = $hash;
            }
        }
        return $missing;
    }

    public function getIndex($data)
    {
        return User::rightJoin('drawings', 'users.phone_hash', '=', 'drawings.phone_hash')
                       ->where('users.id', '=', $data['userid'])
                       ->orderBy('drawings.created_at', 'desc')
                       ->select(
                            'users.username',
                            'users.name',
                            'drawings.id',
                            'drawings.width',
                            'drawings.height',
                            'drawings.line_color',
                            'drawings.bg_color',
                            'drawings.drawing',
                            'drawings.version',
                            'drawings.created_at')
                       ->get();
    }
}