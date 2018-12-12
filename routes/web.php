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

Route::post('/upload', function () {


    $validator = Validator::make(request()->all(), [
        'image' => [
            // 必須
            'required',
            // アップロードされたファイルであること
            'file',
            // 画像ファイルであること
            'image',
            // MIMEタイプを指定
            'mimes:jpeg,png',
            // 最小縦横80px 最大縦横2000px
            'dimensions:min_width=80,min_height=80,max_width=2000,max_height=2000',
        ]
    ]);

    if($validator->fails()) {
        return;
    }

    $path = Storage::disk('s3')->putFile('uploads', request('image'));

    return $path;
});