<?php namespace Fetch\v1\Models;

class User extends \Eloquent {
	protected $fillable = ['username', 'name', 'phone', 'country_code', 'phone_hash', 'token'];
}