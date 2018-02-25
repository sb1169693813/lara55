<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public static function boot()
    {
      parent::boot();

      static::creating(function ($user) {
        $user->activation_token = str_random(30);
      });
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * 一个用户拥有多个微博 
     */
    public function statuses()
    {
        return $this->hasMany('App\Models\Status');
    }

    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }

    //在 Laravel 中会默认将两个关联模型的名称进行合并并按照字母排序，因此我们生成的关联关系表名称会是 followers_user。我们也可以自定义生成的名称，把关联表名改为 followers。
    //belongsToMany 方法的第三个参数 user_id 是定义在关联中的模型外键名，而第四个参数 follower_id 则是要合并的模型外键名。
    //粉丝
    public function followers()
    {
        return $this->belongsToMany('App\Models\User', 'followers', 'user_id', 'follower_id');
    }
    //关注者
    public function followings()
    {
        return $this->belongsToMany('App\Models\User', 'followers', 'follower_id', 'user_id');
    }

    //关注工作
    //为了解决这种问题，我们可以使用 sync 方法。sync 方法会接收两个参数，第一个参数为要进行添加的 id，第二个参数则指明是否要移除其它不包含在关联的 id 数组中的 id，true 表示移除，false 表示不移除，默认值为 true。由于我们在关注一个新用户的时候，仍然要保持之前已关注用户的关注关系，因此不能对其进行移除，所以在这里我们选用 false。现在让我们尝试使用 sync 方法来关注 id 为 3 的用户：
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = [$user_ids];
        }
        
        $this->followings()->sync($user_ids, false);
    }

    //移除关注
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = [$user_ids];
        }
        $this->followings()->detach($user_ids);
    }

    //接下来我们还需要一个方法用于判断当前登录的用户 A 是否关注了用户 B，代码实现逻辑很简单，我们只需要判断用户 B 是否包含在用户 A 的关注人列表上即可。这里我们将用到 contains 方法来做判断。
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
