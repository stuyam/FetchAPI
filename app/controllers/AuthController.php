<?php

use Fetch\Services\Validator;

class AuthController extends \BaseController {

    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

	public function store(){
        $number = Input::get('number');
        $countryCode = Input::get('countryCode');

        if( ! $this->validator->authStore(Input::all()) )
        {
            return Response::make($this->validator->errors());
        }

        $this->addNumberToVerify($number, $this->createVerifyKey(), 0, $countryCode);


        //SEND TEXT MESSAGE TO TWILLIO!


        return Response::json(['complete'=> TRUE]);
    }

    public function verify(){
        $number = Input::get('number');
        $code = Input::get('code');

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

    public function storeUser(){
        $number = Input::get('number');
        $username = Input::get('username');
        $name = Input::get('name');

        if( ! $this->validator->authStoreUser(Input::all()) )
        {
            return Response::make($this->validator->errors());
        }

        $check = $this->isNumberVerified($number);
        if(! $check ){
            return Response::make('Number has not been validated');
        }

        $this->createUserAccount($username, $name, $number, $check->country_code);

        return Response::make('SUCCESS!');

    }

    private function addNumberToVerify($number, $key, $complete, $countryCode){
        $user = new VerifyPhone;
        $user->phone = $number;
        $user->verify = $key;
        $user->complete = $complete;
        $user->country_code = $countryCode;
        $user->save();
    }

    private function verifyNumberWithCode($number, $code){
        $instance =  VerifyPhone::where('phone', '=', $number)->where('verify', '=', $code)->get();
        if(empty($instance)){
            return FALSE;
        }
        $instance->complete = 1;
        $instance->save();
        return TRUE;
    }

    private function isNumberVerified($number){
        $instance = VerifyPhone::where('phone', '=', $number)->where('complete', '=', 1)->get();
        if(empty($instance))
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
    }

    private function createVerifyKey(){
        return rand(1000, 9999);
    }

}