<?php

use Illuminate\Database\Seeder;
use App\Models\User;
class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    //我们会使用第一个用户对除自己以外的用户进行关注，接着再让所有用户去关注第一个用户。
    public function run()
    {
        $users = User::all();
        $user = User::find(1);
        $user_id = $user->id;
        if (!is_array($user_id)) {
            $user_ids = [$user_id];
        }
        // var_dump($user_ids);
        // exit;
        //获取去除掉 ID 为 1 的所有用户 ID 数组
        $followers = $users->slice(1);
        $follower_ids = $followers->pluck('id')->toArray();

        // var_dump($follower_ids);
        // exit;
        //关注了除了1号用户以外的所有用户
        $user->follow($follower_ids);

        //所有用户都关注1号用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
