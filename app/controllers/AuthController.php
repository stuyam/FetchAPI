<?php

use Fetch\Services\Validator;

class AuthController extends \BaseController {

    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

	public function store(){
        $number = Input::get('phone');
        $countryCode = Input::get('country_code');

        if( ! $this->validator->authStore(Input::all()) )
        {
            return Response::make($this->validator->errors());
        }

        if($this->phoneExists($number))
        {
            return Response::make('number already exists for a user');
        }

        $this->addNumberToVerify($number, $countryCode);

        return Response::json(['complete'=> TRUE]);
    }

    public function verify(){
        $number = Input::get('phone');
        $code = Input::get('pin');

        if( ! $this->validator->authVerify(Input::all()) )
        {
            return Response::make($this->validator->errors());
        }

        if( ! $this->verifyNumberWithCode($number, $code) )
        {
            return Response::make('failed to validate number with code');
        }

        return Response::make('Complete!');
    }

    public function create(){
        $number = Input::get('phone');
        $username = Input::get('username');
        $name = Input::get('name');

        if( ! $this->validator->authStoreUser(Input::all()) )
        {
            return Response::make($this->validator->errors());
        }

        $check = $this->isNumberVerified($number);
        if( ! $check ){
            return Response::make('Number has not been validated');
        }

        $this->createUserAccount($username, $name, $number, $check->country_code);

        return Response::make('SUCCESS!');

    }

    private function addNumberToVerify($number, $countryCode){
        $pin = $this->createVerifyKey();
        $tempuser = VerifyPhone::where('phone', '=', $number)->first();;
        if($tempuser)
        {
            $tempuser->verify = $pin;
            $tempuser->save();
        }
        else
        {
            $tempuser = new VerifyPhone;
            $tempuser->phone = $number;
            $tempuser->verify = $pin;
            $tempuser->complete = 0;
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
        $instance =  VerifyPhone::where('phone', '=', $number)->where('verify', '=', $code)->first();
        if( ! $instance){
            return FALSE;
        }
        $instance->complete = 1;
        $instance->save();
        return TRUE;
    }

    private function phoneExists($number)
    {
        return User::where('phone', '=', $number)->count() > 0 ? TRUE : FALSE;
    }

    private function isNumberVerified($number){
        $instance = VerifyPhone::where('phone', '=', $number)->where('complete', '=', 1)->first();
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
        $user->phone_hash = md5($number);
        $user->token = md5(uniqid());
        $user->save();
        VerifyPhone::where('phone', '=', $number)->delete();
    }

    private function createVerifyKey(){
        return rand(1000, 9999);
    }

}