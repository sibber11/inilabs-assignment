<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// simply redirect all the request to todos.index route
Route::get('/', function () {
    return to_route('todos.index');
})->name('home');

// a delete route to delete requested todos
Route::delete('todos/delete-many', [TodoController::class, 'destroyMany'])->name('todos.destroy-many');

// a delete route to delete all completed todos
Route::delete('todos/delete-completed', [TodoController::class, 'destroyCompleted'])->name('todos.destroy-completed');

// a patch route to complete requested todos
Route::patch('todos/complete-many', [TodoController::class, 'completeMany'])->name('todos.complete-many');

// a resource route for todos
Route::resource('todos', TodoController::class);
