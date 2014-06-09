<?php namespace Fetch\v1\Models;

class VerifyPhone extends \Eloquent {

    protected $table = 'verify';

	protected $fillable = ['phone', 'country_code', 'verify', 'expire', 'tries', 'token'];
}