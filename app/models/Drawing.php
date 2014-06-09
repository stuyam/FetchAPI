<?php

class Drawing extends \Eloquent {
	protected $fillable = ['from_userid', 'to_userid', 'drawing', 'read'];
}