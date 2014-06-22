<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLinkableDrawingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('linkable_drawings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
            $table->text('drawing');
            $table->integer('width');
            $table->integer('height');
            $table->string('bg_color', 10);
            $table->string('line_color', 10);
            $table->tinyInteger('version');
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
		Schema::drop('linkable_drawings');
	}

}
