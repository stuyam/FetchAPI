<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDrawingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('drawings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('userid');
			$table->string('to_phone_hash', 40)->index();
			$table->text('drawing');
			$table->boolean('read');
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
		Schema::drop('drawings');
	}

}
