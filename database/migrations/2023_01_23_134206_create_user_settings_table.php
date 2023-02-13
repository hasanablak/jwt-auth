<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public $currentSettings = [
		"two_fa_gsm"	=>	"0",
		"two_fa_mail"	=>	"0",
		"countries"		=>	"90",
		"language"		=>	"tr"
	];
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_settings', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('key');
			$table->string('value');
			$table->boolean("is_hidden")->default(0);
			$table->timestamps();
		});

		foreach ($this->currentSettings as $key => $value) {
			DB::table('user_settings')->insert([
				"user_id" => 1,
				"key"	=>	$key,
				"value"	=>	$value,
				"is_hidden" => "1"
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
		Schema::dropIfExists('user_settings');
	}
};
