<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/students', [StudentsController::class, 'findAll']) -> name('students-find-all');

Route::get('/student/{id}', [StudentsController::class, 'findOne']) -> name('student-find-one');

Route::post('/student', [StudentsController::class, 'create']) -> name('student-create');

Route::patch('/student/{id}', [StudentsController::class, 'update']) -> name('student-update');

Route::delete('/student/{id}', [StudentsController::class, 'delete']) -> name('student-delete');
