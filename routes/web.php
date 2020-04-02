<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Activation user.
Route::get('activate/{id}/{token}', 'Auth\RegisterController@activation')->name('activation');

//Profile
Route::get('profile/{id}', [
    'middleware' => 'auth',
    'uses' => 'UserController@showProfile'
]);
//
Route::get('/', 'PostController@index');
Route::get('/home', ['as' => 'home', 'uses' => 'PostController@index']);

//authentication
//Route::controllers([
//    'auth' => 'Auth\AuthController',
//    'password' => 'Auth\PasswordController',
//]);

Route::group(['middleware' => ['auth']], function() {
    // showing a new post form
    Route::get('new-post', 'PostController@create');
    // save new post
    Route::post('new-post', 'PostController@store');
    // edite post
    Route::get('edit/{slug}', 'PostController@edit');
    // update post
    Route::post('update', 'PostController@update');
    //  delete post
    Route::get('delete/{id}', 'PostController@destroy');
    // show all post user
    Route::get('my-all-posts', 'UserController@user_posts_all');
    // show user drafts
    Route::get('my-drafts', 'UserController@user_posts_draft');
    // add comments
    Route::post('comment/add', 'CommentController@store');
    // delete comments
    Route::post('comment/delete/{id}', 'CommentController@distroy');
});
// profile
Route::get('user/{id}', 'UserController@profile')->where('id', '[0-9]+');
// list posts
Route::get('user/{id}/posts', 'UserController@user_posts')->where('id', '[0-9]+');
// show one post
Route::get('/{slug}', ['as' => 'post', 'uses' => 'PostController@show'])->where('slug', '[A-Za-z0-9-_]+');
