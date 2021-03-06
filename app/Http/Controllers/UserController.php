<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests;
use Auth;
use Mail;

class UserController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth', [
        'except' => ['show', 'create', 'store', 'index', 'comfirmEmail'],
      ]);
      $this->middleware('guest', [
        'only' => ['create']
      ]);
    }

    public function index()
    {
      $users = User::paginate(10);
      return view('users.index', ['users' => $users]);
    }

    /**
     * 注册页面
     */
    public function create()
    {
      return view('users.create');
    }

    /**
     * 个人页面
     */
    public function show(User $user)
    {
      $userStatuses = $user->statuses()
                           ->orderBy('created_at', 'desc')
                           ->paginate(5);
      return view('users.show', [
        'user' => $user,
        'userStatuses' => $userStatuses
        ]);
    }

    /**
     * 注册动作
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'name' => 'required|max:50',
        'email' => 'required|email|unique:users|max:255',
        'password' => 'required|confirmed|min:6'
      ]);
      //return $request->all();
      $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

      //Auth::login($user);
      $this->sendEmailConfirmationTo($user);

      // session()->flash('success', '欢迎，您将在这里开启一段新的旅程');
      // return redirect()->route('users.show', ['user' => $user]);
      session()->flash('success', '验证邮件已发送到你的注册邮箱中，请注意查收');
      return redirect()->route('users.show',['user' => $user]);
    }

    /**
    * 发送邮件
    */
    public function sendEmailConfirmationTo($user)
    {
      $view = "emails.confirm";
      $data = compact('user');
      $from = '446352377@qq.com';
      $name = "sunbin";
      $to = $user->email;
      $subject = '感谢注册lara55应用！请确认您的邮箱';

      Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
        $message->from($from, $name)->to($to)->subject($subject);
      });
    }

    /**
     * 验证邮箱
     */
     public function comfirmEmail($token)
     {
       $user = User::where('activation_token',$token)->firstOrFali();
       $user->activated = true;
       $user->activation_token = null;
       $user->save();

       Auth::login($user);

       session()->flash('success', '恭喜您，激活成功');
       return redirect()->route('users.show',['user' => $user]);
     }

    public function edit(User $user)
    {
      $this->authorize('update', $user);
      return view('users.edit', ['user' => $user]);
    }

    public function update(User $user, Request $request)
    {
      $this->validate($request, [
        'name' => 'required|max:50',
        'password' => 'nullable|confirmed|min:6'
      ]);
      $this->authorize('update', $user);

      $data = [];
      $data['name'] = $request->name;
      if ($request->password) {
        $data['password'] = bcrypt($request->password);
      }
      $user->update($data);

      session()->flash('success', '个人资料更新成功');
      return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy(User $user)
    {
      $this->authorize('destroy', $user);
      $user->delete();
      session()->flash('success', '删除用户成功');
      return redirect()->back();
    }
    //关注的人
    public function followings(User $user)
    {
      $users = $user->followings()->paginate(5);
      $title = '关注的人';
      return view('users.show_follow',[
        'users' => $users,
        'title' => $title
      ]);
    }

      // 粉丝
    public function followers(User $user)
    {
      $users = $user->followers()->paginate(5);
      $title = '粉丝';
      return view('users.show_follow',[
        'users' => $users,
        'title' => $title
      ]);
    }
}
