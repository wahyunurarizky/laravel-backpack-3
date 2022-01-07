<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class StudentCourse extends Model
{
    use HasFactory;
    protected $table = 'student_courses';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function () {
            Redis::publish('updated_data', 'student');
        });
        static::updated(function () {
            Redis::publish('updated_data', 'student');
        });
        static::deleted(function () {
            Redis::publish('updated_data', 'student');
        });
    }
}
