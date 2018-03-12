https://gist.github.com/manuelgeek/23a07b9c725bb6f6c589b7fd3644a634 
use the gist link incase read me is not clear	

**First create our
project**  
composer
create-project --prefer-dist laravel/laravel laravel_admin

create database
**laravel_admin**

change .env
credentials

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=laravel_admin

DB_USERNAME=root

DB_PASSWORD=

Edit our
**app\Providers\AppServiceProvider.php** tp avoid the error “key
too long while running migrations..”

use
Illuminate\Support\ServiceProvider;

  

public function boot()

{

Schema::defaultStringLength(191);

}

**Make default
laravel auth**  
  
php
artisan make:auth

**Lets
create our admin side now**

1. Lets first create our Admin
Model

php artisan make:model Admin
-m

the -m means were creating a
migration alongside it. It will create admins table migrations

Replace the Admin.php with the
code below, the same as the Auth\LoginController.php

&lt;?php

namespace App;

use
Illuminate\Notifications\Notifiable;

use
Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends
Authenticatable

{

use Notifiable;

/**

* The attributes that are
mass assignable.

*

* @var array

*/

protected $fillable = [

'name',
'email','password',

];

/**

* The attributes that
should be hidden for arrays.

*

* @var array

*/

protected $hidden = [

'password',
'remember_token',

];

}

2.
Lets create our Admin Middleware, look on laravel.com on middlewares
for more expalanation. Middleware is our security layer, it controls
access.  
  
 php
artisan make:middleware Admin  
  

replace the code in
**\http\middleware\Admin.php** with

&lt;?php

namespace
App\Http\Middleware;

use
Closure;

use
Illuminate\Support\Facades\Auth;

class
Admin

{

public
function handle($request, Closure $next, $guard = 'admin')

{

if
(!Auth::guard('admin')-&gt;check()) {

return
redirect('/admin/login');

}

return
$next($request);

}

}

3.
Lets now register our Middleware in the **app\Http\Kernel.php**
under **protected $routeMiddleware**

'admin'
=&gt; \App\Http\Middleware\Admin::class,

4. Lets Register Our
‘admin’ guard in the **\conig\auth.php**

under authentication
guards lets replace the guards with these, note the word replace;

'guards'
=&gt; [

'web'
=&gt; [

'driver'
=&gt; 'session',

'provider' =&gt; 'users',

],

'admin'
=&gt; [

'driver'
=&gt; 'session',

'provider' =&gt; 'admins',

],

'api'
=&gt; [

'driver'
=&gt; 'token',

'provider' =&gt; 'users',

],

'admin-api'
=&gt; [

'driver'
=&gt; 'token',

'provider' =&gt; 'admins',

],

],

under
providers add;

'admins'
=&gt; [

'driver'
=&gt; 'eloquent',

'model'
=&gt; App\Admin::class,

],

finally
under passwords add;

'admins'
=&gt; [

'provider' =&gt; 'admins',

'table'
=&gt; 'password_resets',

'expire'
=&gt; 60,

],

we
are done with our \config\auth.php

4.
Lets now do our admins migration, replace the up function with this

public
function up()

{

Schema::create('admins',
function (Blueprint $table) {

$table-&gt;increments('id');

$table-&gt;string('name');

$table-&gt;string('email')-&gt;unique();

$table-&gt;string('password');

$table-&gt;rememberToken();

$table-&gt;timestamps();

});

}

5.
We can now make our views, the admin views,  

first let us make
our controller  
php
artisan make:controller Admin/AuthController  
  

this
will handle our admin logins

replace
the** app\Http\Controller\Admin\AuthController.php** with   
  

it
has 3 functions, show view, login and logout

**Lets
Create our admin **login view, in
**\views\admin\auth\login.blade.php**  
  

copy
the **\views/auth\login.blade.php **and replace paste it in the
admin view , 

change
your form route to {{
route('admin.auth.login') }} for us to log in to admin in our
admin login view  
  

we will do our routes last, 

lets
create our AdminController where the redirect will go to

php
artisan make:controller AdminController 

create
this index funtion in it

public
function index()

	{

		return
view('admin.dashboard.index');

	}

we
need to create a view to get** redirected to after admin** logs
in  
  

, lets do it

create
a file in **\views\admin\dashboard\index.blade.php**

&lt;!DOCTYPE
html&gt;

&lt;html
lang="{{ app()-&gt;getLocale() }}"&gt;

&lt;head&gt;

&lt;meta
charset="utf-8"&gt;

&lt;meta
http-equiv="X-UA-Compatible" content="IE=edge"&gt;

&lt;meta
name="viewport" content="width=device-width,
initial-scale=1"&gt;

&lt;!--
CSRF Token --&gt;

&lt;meta
name="csrf-token" content="{{ csrf_token() }}"&gt;

&lt;title&gt;{{
config('app.name', 'Laravel') }}&lt;/title&gt;

&lt;!--
Styles --&gt;

&lt;link
href="{{ asset('css/app.css') }}" rel="stylesheet"&gt;

&lt;/head&gt;

&lt;body&gt;

&lt;div
id="app"&gt;

&lt;nav
class="navbar navbar-expand-md navbar-light navbar-laravel"&gt;

&lt;div
class="container"&gt;

&lt;a
class="navbar-brand" href="{{ url('/') }}"&gt;

{{ config('app.name',
'Laravel') }}

&lt;/a&gt;

&lt;button
class="navbar-toggler" type="button"
data-toggle="collapse"
data-target="#navbarSupportedContent"
aria-controls="navbarSupportedContent"
aria-expanded="false" aria-label="Toggle navigation"&gt;

&lt;span
class="navbar-toggler-icon"&gt;&lt;/span&gt;

&lt;/button&gt;

&lt;div
class="collapse navbar-collapse"
id="navbarSupportedContent"&gt;

&lt;!-- Left Side Of Navbar
--&gt;

&lt;ul class="navbar-nav
mr-auto"&gt;

&lt;/ul&gt;

&lt;!-- Right Side Of Navbar
--&gt;

&lt;ul class="navbar-nav
ml-auto"&gt;

&lt;!-- Authentication
Links --&gt;

&lt;li class="nav-item
dropdown"&gt;

&lt;a
id="navbarDropdown" class="nav-link dropdown-toggle"
href="#" role="button" data-toggle="dropdown"
aria-haspopup="true" aria-expanded="false"&gt;

{{
Auth::guard('admin')-&gt;user()-&gt;name }} &lt;span
class="caret"&gt;&lt;/span&gt;

&lt;/a&gt;

&lt;div
class="dropdown-menu" aria-labelledby="navbarDropdown"&gt;

&lt;a
href="{{ route('admin.auth.logout') }}"&gt; &lt;i class="fa
fa-power-off"&gt;&lt;/i&gt;

Logout

&lt;/a&gt;

&lt;/div&gt;

&lt;/li&gt;

&lt;/ul&gt;

&lt;/div&gt;

&lt;/div&gt;

&lt;/nav&gt;

&lt;main
class="py-4"&gt;

&lt;div
class="container"&gt;

&lt;div
class="row justify-content-center"&gt;

&lt;div class="col-md-8"&gt;

&lt;div class="card"&gt;

&lt;div
class="card-header"&gt;Dashboard&lt;/div&gt;

&lt;div
class="card-body"&gt;

You are logged in
as ADMIN!

&lt;/div&gt;

&lt;/div&gt;

&lt;/div&gt;

&lt;/div&gt;

&lt;/div&gt;

&lt;/main&gt;

&lt;/div&gt;

&lt;!--
Scripts --&gt;

&lt;script
src="{{ asset('js/app.js') }}"&gt;&lt;/script&gt;

&lt;/body&gt;

&lt;/html&gt;

a
simple page as such

7.
Now Lets Register our routes, in **routes\web.php**

Route::get('/admin/logout',['uses'=&gt;'Admin\AuthController@logout','as'=&gt;'admin.auth.logout']);

Route::get('/admin/login',
['uses'=&gt;'Admin\AuthController@showLoginForm','as'=&gt;'admin.auth.login']);

Route::post('/admin/login',
'Admin\AuthController@login');

//
all protected middleare routes goes here...

Route::middleware('admin')-&gt;group(
function () {

	Route::get('/admin',
'AdminController@index')-&gt;name('admin');

});

Finally

**8.
Lets create a seed in our \database\seed\DataBaseSeeder.php**

this
will create our db account

replace
the run function with this

public
function run()

{

//
$this-&gt;call(UsersTableSeeder::class);

\App\Admin::create(

[

'name' =&gt; 'ADMIN',

'email' =&gt;
'admin@mail.com',

'password' =&gt;
bcrypt('admin'),

]

);

}

9.
**Lets now run our migrations**

php artisan migrate –seed

--seed runs the seeds already,
in you db the admin account is already there.

WE have our admin side finally,
awesome.
