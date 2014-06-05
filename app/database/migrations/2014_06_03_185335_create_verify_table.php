<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVerifyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('verify', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('phone')->unique();
			$table->string('country_code');
			$table->integer('verify');
			$table->boolean('complete');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('verify');
	}

}
