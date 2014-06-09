<?php

class User extends \Eloquent {
	protected $fillable = ['username', 'name', 'phone', 'country_code', 'phone_hash', 'token'];
}