<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    /*  use HasFactory;
    protected $casts = [
        'is_complete' => 'boolean',
    ]; */

    use SoftDeletes;

    protected $dates = ['deleted_at'];


    protected $fillable = ['user_id', 'title', 'content', 'is_completed', 'status', 'task_date', 'created_at'];

    // protected $casts = [
    //     'task_date' => 'datetime:d/m/Y', // Change your format
    // ];

    public function user()
    {

        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
