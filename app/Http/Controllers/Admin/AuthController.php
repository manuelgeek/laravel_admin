<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;
 
    protected $redirectTo = '/admin';
    protected function guard()
    {
      return Auth::guard('admin');
    }
    public function showLoginForm()
    {
    	if(Auth::guard('admin')->check()) 
    	{
    		return redirect('/admin');
    	}
    	
    	return view('admin/auth/login');
    }

    public function logout()
    {
    	Auth::guard('admin')->logout();
    	return redirect('admin/login');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
