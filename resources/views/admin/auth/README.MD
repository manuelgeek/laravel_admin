**First create our project**\
composer create-project --prefer-dist laravel/laravel laravel_admin

create database **laravel_admin**

change .env credentials

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=laravel_admin

DB_USERNAME=root

DB_PASSWORD=

Edit our **app\Providers\AppServiceProvider.php** tp avoid the error "key too long while running migrations.."

use Illuminate\Support\ServiceProvider;\
public function boot()

{

Schema::defaultStringLength(191);

}

**Make default laravel auth**

php artisan make:auth

**Lets create our admin side now**

1\. Lets first create our Admin Model

php artisan make:model Admin -m

the -m means were creating a migration alongside it. It will create admins table migrations

Replace the Admin.php with the code below, the same as the Auth\LoginController.php

<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable

{

use Notifiable;

/**

* The attributes that are mass assignable.

*

* @var array

*/

protected $fillable = [

'name', 'email','password',

];

/**

* The attributes that should be hidden for arrays.

*

* @var array

*/

protected $hidden = [

'password', 'remember_token',

];

}

2. Lets create our Admin Middleware, look on laravel.com on middlewares for more expalanation. Middleware is our security layer, it controls access.

php artisan make:middleware Admin

replace the code in **\http\middleware\Admin.php** with

<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;

class Admin

{

public function handle($request, Closure $next, $guard = 'admin')

{

if (!Auth::guard('admin')->check()) {

return redirect('/admin/login');

}

return $next($request);

}

}

3. Lets now register our Middleware in the **app\Http\Kernel.php** under **protected $routeMiddleware**

'admin' => \App\Http\Middleware\Admin::class,

4\. Lets Register Our 'admin' guard in the **\conig\auth.php**

under authentication guards lets replace the guards with these, note the word replace;

'guards' => [

'web' => [

'driver' => 'session',

'provider' => 'users',

],

'admin' => [

'driver' => 'session',

'provider' => 'admins',

],

'api' => [

'driver' => 'token',

'provider' => 'users',

],

'admin-api' => [

'driver' => 'token',

'provider' => 'admins',

],

],

under providers add;

'admins' => [

'driver' => 'eloquent',

'model' => App\Admin::class,

],

finally under passwords add;

'admins' => [

'provider' => 'admins',

'table' => 'password_resets',

'expire' => 60,

],

we are done with our \config\auth.php

4. Lets now do our admins migration, replace the up function with this

public function up()

{

Schema::create('admins', function (Blueprint $table) {

$table->increments('id');

$table->string('name');

$table->string('email')->unique();

$table->string('password');

$table->rememberToken();

$table->timestamps();

});

}

5. We can now make our views, the admin views,\
first let us make our controller\
php artisan make:controller Admin/AuthController

this will handle our admin logins

replace the **app\Http\Controller\Admin\AuthController.php** with

it has 3 functions, show view, login and logout

**Lets Create our admin** login view, in **\views\admin\auth\login.blade.php**

copy the **\views/auth\login.blade.php** and replace paste it in the admin view ,

change your form route to {{ route('admin.auth.login') }} for us to log in to admin in our admin login view

we will do our routes last,

lets create our AdminController where the redirect will go to

php artisan make:controller AdminController

create this index funtion in it

public function index()

{

return view('admin.dashboard.index');

}

we need to create a view to get **redirected to after admin** logs in

, lets do it

create a file in **\views\admin\dashboard\index.blade.php**

<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">

<head>

<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<!-- Styles -->

<link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>

<div id="app">

<nav class="navbar navbar-expand-md navbar-light navbar-laravel">

<div class="container">

<a class="navbar-brand" href="{{ url('/') }}">

{{ config('app.name', 'Laravel') }}

</a>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">

<!-- Left Side Of Navbar -->

<ul class="navbar-nav mr-auto">

</ul>

<!-- Right Side Of Navbar -->

<ul class="navbar-nav ml-auto">

<!-- Authentication Links -->

<li class="nav-item dropdown">

<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

{{ Auth::guard('admin')->user()->name }} <span class="caret"></span>

</a>

<div class="dropdown-menu" aria-labelledby="navbarDropdown">

<a href="{{ route('admin.auth.logout') }}"> <i class="fa fa-power-off"></i>

Logout

</a>

</div>

</li>

</ul>

</div>

</div>

</nav>

<main class="py-4">

<div class="container">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card">

<div class="card-header">Dashboard</div>

<div class="card-body">

You are logged in as ADMIN!

</div>

</div>

</div>

</div>

</div>

</main>

</div>

<!-- Scripts -->

<script src="{{ asset('js/app.js') }}"></script>

</body>

</html>

a simple page as such

7. Now Lets Register our routes, in **routes\web.php**

Route::get('/admin/logout',['uses'=>'Admin\AuthController@logout','as'=>'admin.auth.logout']);

Route::get('/admin/login', ['uses'=>'Admin\AuthController@showLoginForm','as'=>'admin.auth.login']);

Route::post('/admin/login', 'Admin\AuthController@login');

// all protected middleare routes goes here...

Route::middleware('admin')->group( function () {

Route::get('/admin', 'AdminController@index')->name('admin');

});

Finally

**8. Lets create a seed in our \database\seed\DataBaseSeeder.php**

this will create our db account

replace the run function with this

public function run()

{

// $this->call(UsersTableSeeder::class);

\App\Admin::create(

[

'name' => 'ADMIN',

'email' => 'admin@mail.com',

'password' => bcrypt('admin'),

]

);

}

9. **Lets now run our migrations**

php artisan migrate --seed

--seed runs the seeds already, in you db the admin account is already there.

WE have our admin side finally, awesome.