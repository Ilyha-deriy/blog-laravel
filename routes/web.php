<?php

use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;


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

Route::get('/', [PagesController::class, 'index']);

Route::resource('/blog', PostsController::class);

Route::post('/blog/{post:slug}/comments', [PostCommentsController::class, 'store']);
Route::delete('/{comment:id}', [PostCommentsController::class, 'destroy'])->middleware('admin');
Route::delete('/{reply:id}', [PostCommentsController::class, 'destroy'])->middleware('admin');

Route::get('/blog/create', [PostsController::class, 'create'])->middleware('admin');

Route::get('/blog', [PostsController::class, 'search']);

Route::get('/blog/{post:slug}', [PostsController::class, 'show']);


Route::get('/blog/category/{category:slug}', [PostsController::class, 'category']);


Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

