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
			$table->boolean('is_admin')->default(0);
		});

		$user = User::create([
			"name"	=>	"Yönetici",
			"surname"	=>	"Admin",
			"email"	=>	"yoneticiadmin@xxx.com",
			"password"	=>	"123456",
			"is_admin"	=> 1
		]);
		$user->email_verified_at = now();
		$user->save();
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
