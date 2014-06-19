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
			$table->integer('user_id');
			$table->string('to_phone_hash', 40)->index();
			$table->text('drawing');
            $table->integer('width');
            $table->integer('height');
            $table->string('bg_color', 10);
            $table->string('line_color', 10);
            $table->tinyInteger('version');
			$table->boolean('read');
			$table->integer('timestamp');
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
