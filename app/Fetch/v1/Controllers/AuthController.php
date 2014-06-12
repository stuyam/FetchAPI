<?php namespace Fetch\v1\Controllers;

use \Fetch\v1\Services\Validator;
use \Fetch\v1\Models\User;
use \Fetch\v1\Models\VerifyPhone;
use \Input, \Sms, \App;


class AuthController extends APIController {

    protected $validator;
    protected $user;
    protected $verifyPhone;
    protected $expiration = 600; //ten minutes
    protected $tries = 4;

    public function __construct(Validator $validator, User $user, VerifyPhone $verifyPhone)
    {
        $this->validator = $validator;
        $this->user = $user;
        $this->verifyPhone = $verifyPhone;
    }

    /**
     * @return mixed
     */
    public function postSetNumber(){
        $data = [
            'phone'       => Input::get('phone'),
            'country_code' => Input::get('country_code'),
        ];

        if( ! $this->validator->authStore($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $pin = $this->createVerifyKey();
        $this->verifyPhone->addNumberToVerify($data['phone'], $data['country_code'], $pin, $this->expiration);

        if( ! App::environment('testing'))
            $this->smsVerifyCode($data['phone'], $pin);

        return $this->respondWithNoContent();
    }

    /**
     * @return mixed
     */
    public function postVerifyNumber(){
        $data = [
            'phone' => Input::get('phone'),
            'pin'   => Input::get('pin'),
        ];

        if( ! $this->validator->authVerify($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $verify = $this->verifyPhone->verifyNumberWithCode($data['phone'], $data['pin'], $this->tries);
        if( ! $verify )
        {
            return $this->respondWith400('Failed to validate number with code.');
        }

        $exists = $this->user->phoneExists($data['phone']);
        if($exists)
        {
            $this->verifyPhone->expirePin($data['phone']);
            return $this->respondWithLoginObject($exists);
        }

        return $this->respond(['pin_token'=>$verify]);
    }

    /**
     * @return mixed
     */
    public function postCreateAccount(){
        $data = [
            'pin_token' => Input::get('pin_token'),
            'phone'     => Input::get('phone'),
            'username'  => Input::get('username'),
            'name'      => Input::get('name'),
        ];

        if( ! $this->validator->authStoreUser($data) )
        {
            return $this->respondMissingParameters($this->validator->errors());
        }

        $check = $this->isNumberVerified($data['phone'], $data['pin_token']);
        if( ! $check ){
            return $this->respondWith400('Number has not been validated');
        }

        $user = $this->user->createUserAccount($data['username'], $data['name'], $data['phone'], $check->country_code);

        $this->verifyPhone->expirePin($data['phone']);

        return $this->respondWithLoginObject($user);

    }

    private function smsVerifyCode($number, $pin){
        Sms::send([
            'to'=>$number,
            'text'=>
                "Hello from Fetch! Please enter the following pin to login to your account: $pin"
        ]);
    }

    private function isNumberVerified($number, $token){
        $instance = VerifyPhone::where('phone', '=', $number)->where('token', '=', $token)->where('expire', '>', time())->first();
        if( ! $instance)
        {
            return FALSE;
        }
        return $instance;
    }

    private function createVerifyKey(){
        return rand(1000, 9999);
    }

}