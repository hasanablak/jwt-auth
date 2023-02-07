required;
[x] opensourcesaver/jwt-auth
[x] Illuminate 9

User modeli oluşturulacak, içerideki user modeline extends edilecek, paketin user modelinin
orjinal extendsi "use Illuminate\Foundation\Auth\User as Authenticatable;" olacak.
Tabi jwt'ye göre ve two fa'ya göre hazır olacak bu işlemler

# Register
[x] Kullanıcı kayıt edip resource ile jwt token dönülecek

# Email Verification

# Login
[x] Login olduğunda da User modelinde claim'lere göre resource oluşturulacak aslında tek bir resource'a bağlanılacak
bu resource ihtiyaçları söylecek (two fa mail veya two fa gsm gibi)

# Forgot Password
[x] Bu laravel'in kendi dökümantasyonuna göre yapılacak

# Reset Password
[x] Bu da laravel'in kendi dökümantasyonuna göre yapılacak, Mail gönderilecek

# Two Fa Mail 
[x]
user settings tablosu oluşturulacak ve orada tutulacak
Helper.php yazılacak
User Modeli içinden settings'lere erişim verilecek

# Two Fa Gsm
[x]
user settings tablosu oluşturulacak ve orada tutulacak
Helper.php yazılacak
User Modeli içinden settings'lere erişim verilecek

# TwoFaValidateMiddleware, BasicTokenMiddleware
[x] Kullanıcının girişini ve two ilişkilerini kontrol edecek ona göre uyarı mesajı verecek
Two fa ile doğrulama yapariken basic token middleware ile korunur 
ama hatalı işte ne gibi? adamın mail two fası olsa ve birinden önce diğerini yapmış olsa gibi

"C:\Develop\Laravel\cointro-api\app\Http\Middleware\BasicTokenMiddleware.php"
ve

Bu da içeriye girdikten sonra nerdeyse her route'un bununla korunması lazım tabi kullanıcı bunu kendi routelarına da uygulaması gerekiyor.
Belki bunun için RouteServiceProvider'da jwt dye bi alan oluşturulabilir?
"C:\Develop\Laravel\cointro-api\app\Http\Middleware\TwoFaValidateMiddleware.php"

# Helpers.php

[x]
settings map var.

		
	function settingsMap($id, $all = false)
	{

		$id = is_null($id) || empty($id) ? auth('api')->id() : $id;

		$data = UserSettings::where('usersQid', $id)->where(function ($q) use ($all) {
			if (!$all) return $q->where('hidden', 0);
		})->get();

		$data = $data->mapWithKeys(function ($item) {
			return [$item->key => $item->value == 'true' ? (true) : ($item->value	  == 'false' ? false : $item->value)];
		});
		return (object) $data->toArray();
	}


[x] **User** modeli içerisinde de **settings** diye bir method var bunun get'i buradaki settingsMap'i çağırıyor.

*App\Models\Users.php*

	protected function settings()
	{
		return Attribute::make(
			get: fn ($value, $attributes) => settingsMap($attributes["id"], false)
		);
	}


# 1
## Giriş yapan kişinin başka kullanıcı bilgilerine ulaşabilmesi [x]
## Giriş yapan kişinin kendi bilgilerine ulaşabilme [x]
## Giriş yapan kullanıcının kendi bilgilerini düzenleyebilmesi [x]
## Giriş yapan kullanıcının kendi kullanıcı fotoğrafı ekleyebilmesi, değiştirebilmesi [x]
## Şifre değiştirme  [x]
## Şifre değiştirirken, eski şifresini isteyip doğrulama ve yeni girdiği şifreyi iki kez tekrarlatıp emin oldurma [x]

# 2
## E-posta değiştirme
## Gsm değiştirme
## Settings güncelleme

# 3
## Google ile giriş

