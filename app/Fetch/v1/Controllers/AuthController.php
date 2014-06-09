<?php namespace Fetch\v1\Controllers;

use \Fetch\v1\Services\Validator;
use \Input, \Response, \VerifyPhone, \User, \Sms;


class AuthController extends APIController {

    protected $validator;
    protected $expiration = 600; //ten minutes
    protected $tries = 4;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return mixed
     */
    public function postSetNumber(){
        $number = Input::get('phone');
        $countryCode = Input::get('country_code');

        if( ! $this->validator->authStore(Input::all()) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $this->addNumberToVerify($number, $countryCode);

        return $this->respondWithNoContent();
    }

    /**
     * @return mixed
     */
    public function postVerifyNumber(){
        $number = Input::get('phone');
        $code = Input::get('pin');

        if( ! $this->validator->authVerify(Input::all()) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $verify = $this->verifyNumberWithCode($number, $code);
        if( ! $verify )
        {
            return $this->respondWith400('Failed to validate number with code.');
        }

        $this->expirePin($number);

        $exists = $this->phoneExists($number);
        if($exists)
        {
            return $this->respondWithLoginObject($exists);
        }

        return $this->respond(['pin_token'=>$verify]);
    }

    /**
     * @return mixed
     */
    public function postCreateAccount(){
        $token = Input::get('pin_token');
        $number = Input::get('phone');
        $username = Input::get('username');
        $name = Input::get('name');

        if( ! $this->validator->authStoreUser(Input::all()) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $check = $this->isNumberVerified($number, $token);
        if( ! $check ){
            return $this->respondWith400('Number has not been validated');
        }

        $user = $this->createUserAccount($username, $name, $number, $check->country_code);

        $this->expirePin($number);

        return $this->respondWithLoginObject($user);

    }

    private function expirePin($number)
    {
        $expire = VerifyPhone::where('phone', '=', $number)->first();
        $expire->expire = 0;
        $expire->save();
    }

    private function addNumberToVerify($number, $countryCode){
        $pin = $this->createVerifyKey();
        $tempuser = VerifyPhone::where('phone', '=', $number)->first();;
        if($tempuser)
        {
            $tempuser->verify = $pin;
            $tempuser->expire = time() + $this->expiration;
            $tempuser->tries = 0;
            $tempuser->token = NULL;
            $tempuser->save();
        }
        else
        {
            $tempuser = new VerifyPhone;
            $tempuser->phone = $number;
            $tempuser->verify = $pin;
            $tempuser->expire = time() + $this->expiration;
            $tempuser->tries = 0;
            $tempuser->country_code = $countryCode;
            $tempuser->save();
        }
        $this->smsVerifyCode($number, $pin);
    }

    private function smsVerifyCode($number, $pin){
        Sms::send([
            'to'=>$number,
            'text'=>
                "Hello from Fetch! Please enter the following pin to login to your account: $pin"
        ]);
    }

    private function verifyNumberWithCode($number, $code){
        $instance =  VerifyPhone::where('phone', '=', $number)->where('expire', '>', time())->first();
        if( ! $instance )
        {
            return FALSE;
        }
        if($instance->verify != $code || $instance->tries > $this->tries)
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

    private function phoneExists($number)
    {
        return User::where('phone', '=', $number)->first() ?: FALSE;
    }

    private function isNumberVerified($number, $token){
        $instance = VerifyPhone::where('phone', '=', $number)->where('token', '=', $token)->where('expire', '>', time())->first();
        if( ! $instance)
        {
            return FALSE;
        }
        return $instance;
    }

    private function createUserAccount($username, $name, $number, $country_code)
    {
        $user = new User;
        $user->username = $username;
        $user->name = $name;
        $user->phone = $number;
        $user->country_code = $country_code;
        $user->phone_hash = sha1($number);
        $user->token = sha1(uniqid('m39jSUHDh3asdj3', TRUE));
        $user->save();
        return $user;
    }

    private function createVerifyKey(){
        return rand(1000, 9999);
    }

}