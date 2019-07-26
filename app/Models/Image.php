<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $fillable=['type','path'];

    /**
     * 和User模型一对一关联
     *
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
