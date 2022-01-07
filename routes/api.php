<?php

use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('student', function () {
    $students = Student::with('courses')->get();
    return $students;
});

Route::get('course', function () {
    $courses = Course::with('students')->with('teachers')->get();
    return $courses;
});

Route::get('teacher', function () {
    $teachers = Teacher::with('courses')->get();
    return $teachers;
});

// Route::get('student', function () {
//     $students = Student::with('courses')->get();
//     return $students;
// });
