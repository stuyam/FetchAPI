<?php namespace Fetch\v1\Models;

class VerifyPhone extends \Eloquent {

    protected $table = 'verify';

	protected $fillable = ['phone', 'country_code', 'verify', 'expire', 'tries', 'token'];

    public function addNumberToVerify($number, $countryCode, $pin, $expiration)
    {
        $user = VerifyPhone::where('phone', '=', $number)->first();;
        if($user)
        {
            $user->verify = $pin;
            $user->expire = time() + $expiration;
            $user->tries = 0;
            $user->token = NULL;
            $user->save();
        }
        else
        {
            $user = new VerifyPhone;
            $user->phone = $number;
            $user->verify = $pin;
            $user->expire = time() + $expiration;
            $user->tries = 0;
            $user->country_code = $countryCode;
            $user->save();
        }
    }

    public function expirePin($number)
    {
        $expire = VerifyPhone::where('phone', '=', $number)->first();
        $expire->expire = 0;
        $expire->save();
    }

    public function verifyNumberWithCode($number, $code, $tries){
        $instance =  VerifyPhone::where('phone', '=', $number)->where('expire', '>', time())->first();
        if( ! $instance )
        {
            return FALSE;
        }
        if($instance->verify != $code || $instance->tries > $tries)
        {
            $instance->tries++;
            $instance->save();
            return FALSE;
        }
        $instance->token = sha1(uniqid('h493h4tD42jfsw', TRUE));
        $instance->tries++;
        $instance->save();
        return $instance->token;
    }
}