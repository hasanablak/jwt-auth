<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hasanablak\JwtAuth\Models\User;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function ($table) {
			$table->dropColumn('email');
		});

		Schema::table('users', function (Blueprint $table) {
			$table->string('surname');
			$table->string('username')->unique();
			$table->string('avatar')->nullable();
			$table->string('email')->nullable()->unique();
			$table->string('gsm')->nullable()->unique();
			$table->timestamp('gsm_verified_at')->nullable();
		});

		User::create([
			"name"	=>	"Hasan",
			"surname"	=>	"Ablak",
			"email"	=>	"0hasanablak@gmail.com",
			"password"	=>	"123456"
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}
};
