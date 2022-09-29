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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/students', [StudentsController::class, 'index']) -> name('students-index');

Route::get('/students/{id}', [StudentsController::class, 'show']) -> name('student-show');

Route::get('/students/{id}/image', [StudentsController::class, 'showImage']) -> name('student-show-image');

Route::post('/students', [StudentsController::class, 'create']) -> name('student-create');

Route::put('/students/{id}', [StudentsController::class, 'update']) -> name('student-update');

Route::delete('/students/{id}', [StudentsController::class, 'delete']) -> name('student-delete');
