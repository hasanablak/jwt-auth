<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public $defaultValue = [
		"Send Code To Current Mail For Change Mail",
		"Send Code To New Mail For Validation New Mail",
		"Two Factor Mail Auth",
		"Sign In Confirm Mail",
		"Email sent to old email after email change",
		"Send code to current gsm",
		"Send code to new gsm",
		"Two Factor GSM Auth"
	];

	public function up()
	{
		Schema::create('log_send_out_types', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->timestamps();
		});

		foreach ($this->defaultValue as $value) {
			DB::table('log_send_out_types')->insert([
				"name" => $value
			]);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
};
