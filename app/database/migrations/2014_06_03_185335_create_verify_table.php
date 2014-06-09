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
			$table->string('phone', 20)->unique();
			$table->string('country_code', 10);
			$table->smallInteger('verify');
            $table->integer('expire');
            $table->smallInteger('tries');
			$table->string('token', 40)->nullable();
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
