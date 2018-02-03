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

Route::get('/', function(){

    // 选择账套
    $books = \Encore\Admin\Book\BookModel::query()->get();

    $map = [
        'ranto' => $books->where('code', 'ranto')->first(),
        'sunfen' =>$books->where('code', 'sunfen')->first(),
        'cj' => [],
    ];

    return view('bill_books', compact('books', 'map'));
});
