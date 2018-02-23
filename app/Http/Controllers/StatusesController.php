<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
	//由前面开发用户相关功能的经验可知，一些需要用户登录之后才能执行的请求需要通过中间件来过滤，接下来我们需要开发的 store 和 destroy 动作将用于对微博的创建和删除，这两个动作都需要用户登录，因此让我们借助 Auth 中间件来为这两个动作添加过滤请求。
    public function __construct()
    {
    	$this->middleware('auth');
    }

    public function store(Request $request)
    {
    	//return $request;
    	$this->validate($request,[
    		'content' => 'required|max:140'
    	]);
    	Auth::user()->statuses()->create([
    		'content' => $request->content
    	]);
    	return redirect()->back();
    }

    public function destroy(Status $status)
    {
    	$this->authorize('destory', $status);
    	$status->delete();
    	session()->flash('success', '微博已成功删除');
    	return redirect()->back();
    }
}
