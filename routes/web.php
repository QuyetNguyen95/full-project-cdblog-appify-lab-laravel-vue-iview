<?php

use App\Http\Middleware\AdminCheck;
use Illuminate\Support\Facades\Route;

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




Route::prefix('app')->middleware([AdminCheck::class])->group(function(){

    Route::post('/admin_login','AdminController@adminLogin');


    Route::post('/create_tag','AdminController@addTag');
    Route::post('/edit_tag','AdminController@editTag');
    Route::post('/delete_tag','AdminController@deleteTag');
    Route::get('/get_tags','AdminController@getTags');
    Route::post('/upload','AdminController@upload');
    Route::post('/delete_image','AdminController@deleteImage');

    Route::post('/create_category','AdminController@addCategory');
    Route::get('/get_categories','AdminController@getCategories');
    Route::post('/edit_category','AdminController@editCategory');
    Route::post('/delete_category','AdminController@deleteCategory');


    Route::post('/create_user','AdminController@createUser');
    Route::get('/get_users','AdminController@getUsers');
    Route::post('/edit_user','AdminController@editUser');
    Route::post('/delete_category','AdminController@deleteCategory');

    Route::post('/create_role','AdminController@addRole');
    Route::get('/get_roles','AdminController@getRoles');
    Route::post('/edit_role','AdminController@editRole');
    Route::post('/delete_category','AdminController@deleteCategory');
    Route::post('/assign_roles','AdminController@assignRoles');

    Route::post('/create_blog','AdminController@createBlog');
    Route::get('/get_users','AdminController@getUsers');
    Route::post('/edit_user','AdminController@editUser');
    Route::post('/delete_category','AdminController@deleteCategory');


});

Route::post('/createBlog','AdminController@uploadEditorImage');


// Route::get('/slug','AdminController@slug');

Route::get('/logout','AdminController@logout');
Route::get('/','AdminController@index');
Route::any('{slug}','AdminController@index');



// Route::post('/test','TestController@testMethod');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::any('{slug}',function(){
//     return view('welcome');
// });