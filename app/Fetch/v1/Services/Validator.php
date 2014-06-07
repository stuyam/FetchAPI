<?php namespace Fetch\v1\Services;

use Validator as V;

class Validator {

    protected $errors;

    public function authStore($input)
    {
        $rules = [
            'phone'        => 'required',
            'country_code' => 'required'
        ];

        return $this->validate($input, $rules);
    }

    public function authVerify($input)
    {
        $rules = [
            'phone' => 'required',
            'pin'   => 'required'
        ];

        return $this->validate($input, $rules);
    }

    public function authStoreUser($input)
    {
        $rules = [
            'username' => 'required|alpha_num|min:3|max:16|unique:users',
            'name'     => 'required|min:3|max:30',
            'pin_token'    => 'required',
            'phone'    => 'required',
        ];

        return $this->validate($input, $rules);
    }

    private function validate($input, $rules)
    {
        $validator = V::make($input, $rules);

        if ($validator->fails())
        {
            $this->errors = $validator->messages();
            return FALSE;
        }

        return TRUE;
    }

    public function errors()
    {
        return $this->errors;
    }

} 