<?php namespace Fetch\v1\Models;

class User extends \Eloquent {

	protected $fillable = ['username', 'name', 'phone', 'country_code', 'phone_hash', 'token'];

    public function createUserAccount($username, $name, $number, $country_code)
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

    public function phoneExists($number)
    {
        return User::where('phone', '=', $number)->first() ?: FALSE;
    }
}