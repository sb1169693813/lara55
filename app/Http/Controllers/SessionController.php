<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionController extends Controller
{
    public function __construct()
    {
      $this->middleware('guest', [
        'only' => ['create'],
      ]);
    }
    public function create()
    {
      return view('sessions.create');
    }

    public function store(Request $request)
    {
      $cre = $this->validate($request, [
        'email' => 'required|email|max:255',
        'password' => 'required'
      ]);

      if (Auth::attempt($cre, $request->has('remember'))) {
        //echo "yes";
        session()->flash('success','欢迎回来');
        // return redirect()->route('users.show',[Auth::user()]);
        return redirect()->intended(route('users.show',[Auth::user()]));
      } else {
        //echo "no";
        session()->flash('danger', '很抱歉，你的邮箱和密码不匹配');
        return redirect()->back();
      }
    }

    public function destory()
    {
      Auth::logout();
      session()->flash('success', '您已经成功退出');
      return redirect()->route('login');
    }
}
