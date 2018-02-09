<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'user_id'
    ];

	/**
     * 一个微博属于哪个用户
	 */
    public function user()
    {
    	return $this->belongs('App\Models\User');
    }

}
