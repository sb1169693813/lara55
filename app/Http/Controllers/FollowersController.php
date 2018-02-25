<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
class FollowersController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
    //关注用户
    public function store(User $user)
    {
    	//由于用户不能对自己进行关注和取消关注，因此我们在 store 和 destroy 方法中都对用户身份做了判断，当执行关注和取消关注的用户对应的是当前的用户时，重定向到首页。
    	if (Auth::user()->id === $user->id) {
    		return redirect('/');
    	}
    	//为了使代码逻辑更加严谨，在进行关注和取消关注操作之前，我们还会利用 isFollowing 方法来判断当前用户是否已关注了要进行操作的用户。
    	if (!Auth::user()->isFollowing($user->id)) {
    		Auth::user()->follow($user->id);
    	}

    	return redirect()->route('users.show', $user->id);
    }
    //取消关注用户
    public function destroy(User $user)
    {
    	//由于用户不能对自己进行关注和取消关注，因此我们在 store 和 destroy 方法中都对用户身份做了判断，当执行关注和取消关注的用户对应的是当前的用户时，重定向到首页。
    	if (Auth::user()->id === $user->id) {
    		return redirect('/');
    	}
    	//为了使代码逻辑更加严谨，在进行关注和取消关注操作之前，我们还会利用 isFollowing 方法来判断当前用户是否已关注了要进行操作的用户。
    	if (Auth::user()->isFollowing($user->id)) {
    		Auth::user()->unfollow($user->id);
    	}

    	return redirect()->route('users.show', $user->id);
    }
}
